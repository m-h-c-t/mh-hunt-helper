<?php

require_once "config.php";

main();

function main() {
    global $pdo;
    if (empty($_REQUEST['mice'])) {
        return;
    }

    connectMHHH();
    print getSetups();
}

function connectMHHH() {
    global $pdo, $servername, $dbname, $username, $password;
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}

function getSetups() {
    $mice_names = explode('||', $_REQUEST['mice']);
    $qmarks = implode(',', array_fill(0, count($mice_names), '?'));

    $query = "
SELECT 
    l.id as location_id,
    l.name AS location,
    IFNULL(st.id, 'NA') AS stage_id,
    IFNULL(st.name, 'NA') AS stage,
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
WHERE m.name IN (" . $qmarks . ")
";

    global $pdo;
    $query = $pdo->prepare($query);
    $query->execute($mice_names);
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $found_setups[$row['location_id']]['name'] = $row['location'];
        $found_setups[$row['location_id']]['stages'][$row['stage_id']]['name'] = $row['stage'];
        $found_setups[$row['location_id']]['stages'][$row['stage_id']]['mice'][$row['mouse_id']]['name'] = $row['mouse'];
        $found_setups[$row['location_id']]['stages'][$row['stage_id']]['mice'][$row['mouse_id']]['cheese'][$row['cheese_id']]['name'] = $row['cheese'];
        $found_setups[$row['location_id']]['stages'][$row['stage_id']]['mice'][$row['mouse_id']]['cheese'][$row['cheese_id']]['rate'] = $row['rate']/100;
        $found_setups[$row['location_id']]['stages'][$row['stage_id']]['mice'][$row['mouse_id']]['cheese'][$row['cheese_id']]['total_hunts'] = $row['total_hunts'];
        $found_setups[$row['location_id']]['stages'][$row['stage_id']]['mice'][$row['mouse_id']]['cheese'][$row['cheese_id']]['attracted_hunts'] = $row['attracted_hunts'];
    }
   return "<pre>".json_encode($found_setups, JSON_PRETTY_PRINT)."<pre>";
}


