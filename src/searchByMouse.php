<?php

require_once "config.php";
$results = [];

if (isset($_REQUEST['mice']) && !empty(trim($_REQUEST['mice']))) {
    main();
}

function main() {
    global $pdo, $results;

    connectMHHH();
    $mice = explode("\n", str_replace("\r", "", $_REQUEST['mice']));
    $mice = array_map('clean', $mice);
    $mice = array_values(array_filter($mice));
    $db_results = queryDatabase($mice);
    $results = formResultsArray($db_results, $mice);
}

function clean($mouse) {
    $mouse = trim($mouse);
    $mouse = preg_replace("/\ mouse$/i", "", $mouse);
    return $mouse;
}

function connectMHHH() {
    global $pdo, $servername, $dbname, $username, $password;
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}

function queryDatabase($mice) {
    global $results;
    $placeholders = implode(',', array_fill(0, count($mice), '?'));

    $query = "
SELECT
    l.id as location_id,
    l.name AS location,
    IFNULL(st.id, '...') AS stage_id,
    IFNULL(st.name, '...') AS stage,
    c.id AS cheese_id,
    c.name AS cheese,
    m.id as mouse_id,
    m.name as mouse,
    a.rate,
    a.total_hunts,
    a.attracted_hunts
FROM mhhunthelper.attractions a
INNER JOIN locations l ON a.location_id = l.id
INNER JOIN mice m ON a.mouse_id = m.id
INNER JOIN cheese c ON a.cheese_id = c.id
LEFT JOIN stages st ON a.stage_id = st.id
WHERE m.name IN (" . $placeholders . ")
ORDER BY a.rate DESC";

    global $pdo;
    $query = $pdo->prepare($query);
    $query->execute($mice);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function formResultsArray($db_results, $original_items) {
    $results['found']['items'] = [];
    foreach ($db_results as $row) {
        $results['found']['items'][$row['mouse_id']] = $row['mouse'];
        $results['results'][$row['location_id']]['name'] = $row['location'];
        $results['results'][$row['location_id']]['items_count'][$row['mouse_id']] = 1;
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['name'] = $row['stage'];
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['mouse_id']]['name'] = $row['mouse'];
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['mouse_id']]['cheese'][$row['cheese_id']]['name'] = $row['cheese'];
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['mouse_id']]['cheese'][$row['cheese_id']]['rate'] = $row['rate']/100;
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['mouse_id']]['cheese'][$row['cheese_id']]['total_hunts'] = $row['total_hunts'];
        $results['results'][$row['location_id']]['stages'][$row['stage_id']]['items'][$row['mouse_id']]['cheese'][$row['cheese_id']]['attracted_hunts'] = $row['attracted_hunts'];
    }
    if (isset($results['results']) && !empty($results['results'])) {
        uasort($results['results'], 'cmpLocationMiceCount');
        foreach ($results['results'] as &$location) {
            uasort($location['stages'], 'cmpStageMiceCount');
        }
    }
    $results['found']['count'] = count($results['found']['items']);
    $results['not_found']['items'] = array_udiff($original_items, $results['found']['items'], 'strcasecmp');
    $results['not_found']['count'] = count($results['not_found']['items']);
    $results['original_items'] = $original_items;
    return $results;
}

function cmpLocationMiceCount($a, $b) {
    return $b['items_count'] <=> $a['items_count'];
}

function cmpStageMiceCount($a, $b) {
    return $b['items'] <=> $a['items'];
}
