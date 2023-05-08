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

$daily_data = fetchDailyMarketValuesJson();
$current_sb_price = $daily_data[114]['price'];

setPDOConv();

$query = $pdo_conv->prepare('SELECT id, gold_value, sb_value FROM items');
$query->execute();

while($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $item_id = (int)$row['id'];
    $gold_value = $row['gold_value'] === NULL ? NULL : intval($row['gold_value']);
    $sb_value = $row['sb_value'] === NULL ? NULL : doubleval($row['sb_value']);

    if (array_key_exists($item_id, $daily_data) && !is_null($daily_data[$item_id])) {
        // markethunt entry found, update gold and sb values
        $gold_value = $daily_data[$item_id]['price'];
        $sb_value = $daily_data[$item_id]['sb_price'];
    } else if ($gold_value !== NULL) {
        // has manually entered gold value, update sb value only
        $sb_value = $gold_value / $current_sb_price;
    }

    // persist changes
    $update_query = $pdo_conv->prepare('UPDATE items 
        SET gold_value = :gold_value, sb_value = :sb_value 
        WHERE id = :item_id');
    $update_query->execute(array(
        'gold_value' => $gold_value,
        'sb_value' => $sb_value,
        'item_id' => $item_id,
    ));
}