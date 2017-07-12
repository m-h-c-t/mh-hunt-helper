<?php

require_once "config.php";

main();

function main() {
    global $pdo, $servername, $dbname, $username, $password;
    if (empty($_POST['item_id']) || empty($_POST['item_type'])) {
        return;
    }

    // PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

    $query_all = '';
    $query_one = '';
    switch ($_POST['item_type']) {
        case 'mouse':
            getMouseQuery($query_all, $query_one);
            break;
        case 'mhmh_mouse':
            getMHMHMouseQuery($query_all, $query_one);
            break;
        case 'loot':
            getLootQuery($query_all, $query_one);
            break;
        default:
            return;
    }

    getItem($query_all, $query_one);
}

// Loot rates by location
function getItem($query_all, $query_one) {
    global $pdo;
    if ($_POST['item_id'] === 'all') {
        $query = $pdo->prepare($query_all);
        $query->execute();
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $item_array[] = ["id" => (int)$row['id'], "value" => utf8_encode(stripslashes($row['name']))];
        }
        print json_encode($item_array);
    } else if (!empty($_POST['item_id'])) {
        $query = $pdo->prepare($query_one);
        $query->execute(array($_POST['item_id']));

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
    $query_all = 'SELECT id, name FROM loot';
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
