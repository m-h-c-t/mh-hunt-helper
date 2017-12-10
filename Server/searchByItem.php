<?php

require_once "config.php";

main();

function main() {
    global $pdo;
    if (empty($_REQUEST['item_id']) || empty($_REQUEST['item_type'])) {
        return;
    }

    $query_all = '';
    $query_one = '';
    switch ($_REQUEST['item_type']) {
        case 'mouse':
            connectMHHH();
            getMouseQuery($query_all, $query_one);
            break;
        case 'mhmh_mouse':
            connectMHHH();
            getMHMHMouseQuery($query_all, $query_one);
            break;
        case 'loot':
            connectMHHH();
            getLootQuery($query_all, $query_one);
            break;
        case 'map':
            connectMMS();
            getMapQuery($query_all, $query_one);
            break;
        case 'convertible':
            connectMHC();
            getConvertibleQuery($query_all, $query_one);
            break;
        default:
            return;
    }

    getItem($query_all, $query_one);
}

function connectMHHH() {
    global $pdo, $servername, $dbname, $username, $password;
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}

function connectMMS() {
    global $pdo, $mms_servername, $mms_dbname, $mms_username, $mms_password;
    $pdo = new PDO("mysql:host=$mms_servername;dbname=$mms_dbname;charset=utf8", $mms_username, $mms_password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}

function connectMHC() {
    global $pdo, $convertible_servername, $convertible_dbname, $convertible_username, $convertible_password;
    $pdo = new PDO("mysql:host=$convertible_servername;dbname=$convertible_dbname;charset=utf8", $convertible_username, $convertible_password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}

// Loot rates by location
function getItem($query_all, $query_one) {
    global $pdo;
    if ($_REQUEST['item_id'] === 'all') {
        $query = $pdo->prepare($query_all);
        $query->execute();
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $item_array[] = ["id" => (int)$row['id'], "value" => utf8_encode(stripslashes($row['name']))];
        }
        print json_encode($item_array);
    } else if (!empty($_REQUEST['item_id'])) {
        $query = $pdo->prepare($query_one);
        $query->execute(array($_REQUEST['item_id']));

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        print json_encode($results);
    }
}

function getMouseQuery(&$query_all, &$query_one) {
    $query_all = 'SELECT id, name FROM mice';
    $query_one = '
        SELECT l.name AS location, s.name AS stage, h.total_hunts, h.rate, c.name AS cheese
        FROM attractions h
        INNER JOIN locations l ON h.location_id = l.id
        INNER JOIN mice m ON h.mouse_id = m.id
        INNER JOIN cheese c ON h.cheese_id = c.id
        LEFT JOIN stages s ON h.stage_id = s.id
        WHERE h.mouse_id = ? AND total_hunts > 0';
}

function getLootQuery(&$query_all, &$query_one) {
	# blocking gold
    $query_all = 'SELECT id, name FROM loot where id NOT IN (15, 47, 106, 138, 191, 194, 210, 226, 227, 260, 261, 262, 264, 265)';
    $query_one = '
        SELECT l.name AS location, s.name AS stage, h.total_hunts, h.rate, c.name AS cheese
        FROM drops h
        INNER JOIN cheese c ON h.cheese_id = c.id
        INNER JOIN locations l ON h.location_id = l.id
        LEFT JOIN stages s ON h.stage_id = s.id
        WHERE h.loot_id = ?';
}

function getMHMHMouseQuery(&$query_all, &$query_one) {
    global $mhmh_dbname;
    $query_all = 'SELECT id, name FROM ' . $mhmh_dbname . '.mice';
}

function getMapQuery(&$query_all, &$query_one) {
    $query_all = 'SELECT m.id, m.name FROM mhmapspotter.maps m ORDER BY m.name ASC';
    $query_one = 'SELECT mhmhm.name as mouse, mma.rate, mma.seen_maps, mma.total_maps
        FROM mhmapspotter.map_mice_aggr mma
        INNER JOIN mhmaphelper.mice mhmhm ON mma.mouse_id = mhmhm.id
        WHERE mma.map_type_id = ?
        ORDER BY mma.rate DESC';
}

function getConvertibleQuery(&$query_all, &$query_one) {
    $query_all = 'SELECT c.id, c.name FROM mhconverter.convertibles c ORDER BY c.name ASC';
    $query_one = 'SELECT i.name as item, ca.rate, ca.total
        FROM mhconverter.convertibles_aggr ca
        INNER JOIN mhconverter.items i ON ca.item_id = i.id
        WHERE ca.convertible_id = ?
        ORDER BY ca.rate DESC';
}
