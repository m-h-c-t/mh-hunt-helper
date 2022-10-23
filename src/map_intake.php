<?php
define('not_direct_access', TRUE);
require_once "send_response.php";
require_once "check-ban.php";
require_once "check-cors.php";
require_once "config.php";
require_once "db-connect.php";
require_once "userid-from-hash.php";


$required_fields = [
    'mice'              => 'string', # BLOCKING SCAVENGER MAPS
    'id'                => 'number',
    'name'              => 'string',
    'extension_version' => 'number',
    'hunter_id_hash'    => 'string',
];

foreach ($required_fields as $field => $type) {
    if (empty($_POST[$field])) {
        error_log("$field field is missing (blocking scav maps by mice field)");
        die();
    }
    if ($type == 'number' && !is_numeric($_POST[$field])) {
        error_log("$field field is not numeric");
        die();
    }
}

if (!in_array($_POST['extension_version'], $allowed_extension_versions)) {
    error_log("Bad version: " . $_POST['extension_version']);
    // sendResponse('error', "Please update extension to the latest version (unless testing).");
    die();
}

if ($_POST['name'] == 'Arduous Chrome Map' && (in_array('Dark Templar', $_POST['mice'])
    || in_array('Paladin Weapon Master', $_POST['mice'])
    || in_array('Manaforge Smith', $_POST['mice'])
    || in_array('Hired Eidolon', $_POST['mice'])
    || in_array('Desert Nomad', $_POST['mice']))) {
    // error_log('Old map submitted');
    die();
}

setPDOMap();
$query = $pdo_map->prepare('SELECT 1 FROM map_records WHERE map_id = :id LIMIT 1');
$query->execute(array('id' => $_POST['id']));

if ($query->fetchColumn()) {
    die();
}

$query = $pdo_map->prepare('SELECT 1 FROM map_mice WHERE map_id = :id LIMIT 1');
$query->execute(array('id' => $_POST['id']));

if ($query->fetchColumn()) {
    //error_log("Spotter tried to insert existing map mice again for map id $_POST[id]");
    // Not sure why this even gets hit so much
    die();
}

// Get map id
$query = $pdo_map->prepare('SELECT m.id FROM maps m WHERE m.name LIKE ?');
$query->execute(array($_POST['name']));
$map_type_id = $query->fetchColumn();

if (!$map_type_id) {
    $query = $pdo_map->prepare('INSERT INTO maps (name) VALUES (?)');
    $query->execute(array($_POST['name']));
    $map_type_id = $pdo_map->lastInsertId();
}


// Get mice ids
$mice_supplied_count = count($_POST['mice']);
$mice_ids = [];
foreach ($_POST['mice'] as $mouse_name) {
    $query = $pdo_map->prepare("SELECT m.id FROM mice m WHERE m.name LIKE ?");
    $query->execute(array($mouse_name));
    $mouse_id = $query->fetchColumn();

    if (!$mouse_id) {
        $query = $pdo_map->prepare("INSERT INTO mice (name) VALUES (?)");
        $query->execute(array($mouse_name));
        $mouse_id = $pdo_map->lastInsertId();
    }
    $mice_ids[] = $mouse_id;
}
if ($mice_supplied_count != count($mice_ids)) {
    error_log("Map intake should have found $mice_supplied_count mice ids, but instead found " . count($mice_ids) . " ids, for map id $_POST[id]");
    die();
}


// Record map with mice
$query = $pdo_map->prepare('INSERT INTO map_records (map_id, map_type_id, extension_version, user_id) VALUES (?, ?, ?, ?)');
$query->execute(array($_POST['id'], $map_type_id, $_POST['extension_version'], $user_id));

$insert_query = 'INSERT INTO map_mice (map_id, mouse_id) VALUES (:map_id,';
$insert_query .= implode('),(:map_id,', $mice_ids);
$insert_query .= ')';

$query = $pdo_map->prepare($insert_query);
$query->execute(array('map_id' => $_POST['id']));

$mice_inserted_count = $query->rowCount();
if ($mice_supplied_count != $mice_inserted_count) {
    error_log("Map intake should have inserted $mice_supplied_count mice, but instead inserted $mice_inserted_count, for map id $_POST[id]");
}

sendResponse('success', "Thanks for the map info!");
