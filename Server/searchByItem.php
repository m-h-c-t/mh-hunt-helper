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
        SELECT l.name as location, s.name as stage, h.total_hunts, h.rate, c.name as cheese
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
        SELECT t1.location, t1.stage, t1.cheese, t1.hunts, t2.loot, t2.amount
        FROM
            (SELECT ln.id AS location_id, ln.name AS location, s.id AS stage_id, s.name AS stage, c.id AS cheese_id, c.name AS cheese, count(distinct h.id) AS hunts
            FROM hunts h
            INNER JOIN cheese c ON h.cheese_id = c.id
            INNER JOIN locations ln ON h.location_id = ln.id
            LEFT JOIN stages s ON h.stage_id = s.id
            WHERE h.extension_version >= 11107
            GROUP BY h.location_id, h.stage_id, h.cheese_id) AS t1
        INNER JOIN
            (select h.location_id AS location_id, h.stage_id AS stage_id, h.cheese_id AS cheese_id, l.id AS loot_id, l.name AS loot, sum(hl.amount) AS amount
            FROM hunts h
            INNER JOIN hunt_loot hl ON h.id = hl.hunt_id
            INNER JOIN loot l ON hl.loot_id = l.id
            WHERE h.extension_version >= 11107
            GROUP BY h.location_id, h.stage_id, h.cheese_id, hl.loot_id) AS t2 ON t1.location_id = t2.location_id AND t1.stage_id <=> t2.stage_id AND t1.cheese_id = t2.cheese_id
        WHERE t2.loot_id = ?';
}
