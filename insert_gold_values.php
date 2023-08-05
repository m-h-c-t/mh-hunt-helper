<?php
require_once('src/config.php');
require_once('src/db-connect.php');

global $pdo_conv;

function fetchDailyMarketValuesJson() {
    global $query_user_agent, $markethunt_domain;
    $stream_context = stream_context_create([
        'http' => [
            'user_agent' => $query_user_agent,
            'timeout' => 20 // 20 seconds
        ]
    ]);

    $response = file_get_contents("https://$markethunt_domain/items", false, $stream_context);

    if ($response === false) throw new RuntimeException("Failed to receive response from $markethunt_domain");

    $response_data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

    $data = [];
    foreach ($response_data as $item) {
        $data[$item['item_info']['item_id']] = $item['latest_market_data'];
    }

    return $data;
}

function setGoldValue($connection, int $item_id, int $gold_value) {
    $insert_query = $connection->prepare('INSERT INTO item_markethunt (item_id, gold_value) 
        VALUES (:item_id, :gold_value) ON DUPLICATE KEY UPDATE gold_value = VALUES(gold_value)');
    $insert_query->execute([
        'item_id' => $item_id,
        'gold_value' => $gold_value,
    ]);
}

setPDOConv();

$daily_data = fetchDailyMarketValuesJson();
$latest_sb_price = $daily_data[114]['price'];

// Insert/update gold values from markethunt
foreach ($daily_data as $item_id => $item_data) {
    if ($item_data === NULL) continue;

    setGoldValue($pdo_conv, $item_id, $item_data['price']);
}

// update custom item prices
setGoldValue($pdo_conv, 211, $daily_data[114]['price']); // Magic Essence
setGoldValue($pdo_conv, 431, 1); // Gold

// Update sb values of all items based on their gold values. We do it this way to support custom item prices
$query = $pdo_conv->prepare('SELECT item_id, gold_value, sb_value FROM item_markethunt');
$query->execute();

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $item_id = (int)$row['item_id'];
    $gold_value = $row['gold_value'] === NULL ? NULL : intval($row['gold_value']);

    $update_query = $pdo_conv->prepare('UPDATE item_markethunt 
        SET sb_value = :sb_value 
        WHERE item_id = :item_id');
    $update_query->execute([
        'sb_value' => $gold_value / $latest_sb_price,
        'item_id' => $item_id,
    ]);
}