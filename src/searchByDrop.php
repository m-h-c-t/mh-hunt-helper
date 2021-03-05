<?php

require_once "config.php";
$results = [];



if (isset($_REQUEST['items']) && !empty(trim($_REQUEST['items']))) {
    main();
}

function main() {
    global $pdo, $results;

    connectMHHH();
    $items = explode("\n", str_replace("\r", "", $_REQUEST['items']));
    $items = array_map('clean', $items);
    $items = array_values(array_filter($items));
    $db_results = queryDatabase($items);
    $results = formResultsArray($db_results, $items);
}

function clean($item) {
    $item = trim($item);
    return $item;
}

function connectMHHH() {
    global $pdo, $servername, $dbname, $username, $password;
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}

function queryDatabase($items) {
    global $results;
    $placeholders = implode(',', array_fill(0, count($items), '?'));

    $query = "
SELECT
    l.id as location_id,
    l.name AS location,
    IFNULL(st.id, '...') AS stage_id,
    IFNULL(st.name, '...') AS stage,
    c.id AS cheese_id,
    c.name AS cheese,
    lt.id as item_id,
    lt.name as item,
    d.total_hunts,
    d.total_catches,
    d.drop_count,
    (d.drop_count/d.total_catches*100) as drop_rate
FROM mhhunthelper.drops d
INNER JOIN locations l ON d.location_id = l.id
INNER JOIN loot lt ON d.loot_id = lt.hg_item_id
INNER JOIN cheese c ON d.cheese_id = c.id
LEFT JOIN stages st ON d.stage_id = st.id
WHERE lt.name IN (" . $placeholders . ")
ORDER BY drop_rate DESC";

    global $pdo;
    $query = $pdo->prepare($query);
    $query->execute($items);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function formResultsArray($db_results, $original_items) {
    $results['found']['items'] = [];
    foreach ($db_results as $row) {
        $results['found']['items'][$row['item_id']] = $row['item'];
        $results['results'][$row['location_id']]['name'] = $row['location'];
        $results['results'][$row['location_id']]['items_count'][$row['item_id']] = 1;
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['name'] = $row['stage'];
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['item_id']]['name'] = $row['item'];
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['item_id']]['cheese'][$row['cheese_id']]['name'] = $row['cheese'];
        // $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['item_id']]['cheese'][$row['cheese_id']]['rate'] = $row['rate']/100;
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['item_id']]['cheese'][$row['cheese_id']]['total_hunts'] = $row['total_hunts'];
        // $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['item_id']]['cheese'][$row['cheese_id']]['total_drops'] = $row['total_drops'];
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['item_id']]['cheese'][$row['cheese_id']]['total_catches'] = $row['total_catches'];
        // $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['item_id']]['cheese'][$row['cheese_id']]['rate_per_catch'] = $row['rate_per_catch'];
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['item_id']]['cheese'][$row['cheese_id']]['drop_count'] = $row['drop_count'];
        // $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['item_id']]['cheese'][$row['cheese_id']]['min_amt'] = $row['min_amt'];
        // $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['item_id']]['cheese'][$row['cheese_id']]['max_amt'] = $row['max_amt'];
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['item_id']]['cheese'][$row['cheese_id']]['drop_rate'] = $row['drop_rate'];
    }
    if (isset($results['results']) && !empty($results['results'])) {
        uasort($results['results'], 'cmpLocationitemsCount');
        foreach ($results['results'] as &$location) {
            uasort($location['stages'], 'cmpStageitemsCount');
        }
    }
    $results['found']['count'] = count($results['found']['items']);
    $results['not_found']['items'] = array_udiff($original_items, $results['found']['items'], 'strcasecmp');
    $results['not_found']['count'] = count($results['not_found']['items']);
    $results['original_items'] = $original_items;
    return $results;
}

function cmpLocationitemsCount($a, $b) {
    return $b['items_count'] <=> $a['items_count'];
}

function cmpStageitemsCount($a, $b) {
    return $b['items'] <=> $a['items'];
}
