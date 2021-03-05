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
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}

function connectMMS() {
    global $pdo, $mms_servername, $mms_dbname, $mms_username, $mms_password;
    $pdo = new PDO("mysql:host=$mms_servername;port=$mms_port;dbname=$mms_dbname;charset=utf8", $mms_username, $mms_password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}

function connectMHC() {
    global $pdo, $convertible_servername, $convertible_dbname, $convertible_username, $convertible_password;
    $pdo = new PDO("mysql:host=$convertible_servername;port=$convertible_port;dbname=$convertible_dbname;charset=utf8", $convertible_username, $convertible_password);
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
    $table = generateTable("attractions");

    $query_all = 'SELECT id, name FROM mice';
    $query_one = '
        SELECT l.name AS location, s.name AS stage, h.total_hunts, h.rate, c.name AS cheese
        FROM ' . $table . ' h
        INNER JOIN locations l ON h.location_id = l.id
        INNER JOIN mice m ON h.mouse_id = m.id
        INNER JOIN cheese c ON h.cheese_id = c.id
        LEFT JOIN stages s ON h.stage_id = s.id
        WHERE h.mouse_id = ? AND total_hunts > 0';
}

function getLootQuery(&$query_all, &$query_one) {
    $table = generateTable("drops");

    # blocking gold
    $query_all = 'SELECT hg_item_id as id, name FROM loot UNION SELECT hg_item_id as id, plural_name as name FROM loot';
    $query_one = '
        SELECT l.name AS location, s.name AS stage, h.total_hunts, c.name AS cheese, h.total_catches
          , h.total_drops, ROUND(h.drop_count/h.total_catches*100,2) AS drop_pct, h.min_amt, h.max_amt
        FROM ' . $table . ' h
        INNER JOIN cheese c ON h.cheese_id = c.id
        INNER JOIN locations l ON h.location_id = l.id
        LEFT JOIN stages s ON h.stage_id = s.id
        WHERE h.loot_id = ?';
}

function generateTable($table) {
    $code_names_only = true;
    $silent = true;
    require_once "filters.php";

    if (!empty($_REQUEST['timefilter'])
        && in_array($_REQUEST['timefilter'], $filters)
        && $_REQUEST['timefilter'] != 'all_time') {
        $table .= '_' . $_REQUEST['timefilter'];
    }
    return $table;
}

function getMapQuery(&$query_all, &$query_one) {
    $query_all = 'SELECT m.id, m.name FROM mhmapspotter.maps m ORDER BY m.name ASC';
    $query_one = 'SELECT m.name as mouse, mma.rate, mma.seen_maps, mma.total_maps
        FROM mhmapspotter.map_mice_aggr mma
        INNER JOIN mhmapspotter.mice m ON mma.mouse_id = m.id
        WHERE mma.map_type_id = ?
        ORDER BY mma.rate DESC';
}

function getConvertibleQuery(&$query_all, &$query_one) {
    $query_all = 'SELECT c.id, c.name FROM mhconverter.convertibles c ORDER BY c.name ASC';
    $query_one = 'SELECT aci.convertible_id as conv, i.name as item, 
        aci.total_convertibles_opened as total,	aci.total_item_quantity as total_items,
        aci.single_convertibles_opened as single_opens, aci.times_with_any, 
        aci.min_item_quantity, aci.max_item_quantity, aci.total_quantity_when_any 
        from aggr_convertible_item aci 
	        inner join items i 
		        on aci.item_id = i.id 
        where aci.convertible_id = ?';
}
