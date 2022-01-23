<?php
# In this file mice can be mice or scavenger items
define('not_direct_access', TRUE);
require_once "send_response.php";
require_once "check-ban.php";
require_once "check-cors.php";

if (
    (empty($_POST['mice']) && empty($_POST['items']))               ||
    empty($_POST['id'])                || !is_numeric($_POST['id']) ||
    empty($_POST['name'])              ||
    empty($_POST['extension_version'])) {
    error_log("One of the fields was missing");
    die();
}

require_once "config.php";

if (!is_numeric($_POST['extension_version']) || !in_array($_POST['extension_version'], $allowed_extension_versions)) {
    error_log("Bad version: " . $_POST['extension_version']);
    // sendResponse('error', "Please update extension to the latest version (unless testing).");
    die();
}

$passed_things = [];
if (!empty($_POST['mice'])) {
    $passed_things = $_POST['mice'];
} else if (!empty($_POST['items'])) {
    $passed_things = $_POST['items'];
} else {
    error_log("No mice nor items on submitted map. Skipping.");
    die();
}

# Old Arduous Chrom Map check
if ($_POST['name'] == 'Arduous Chrome Map' && count(array_intersect(
    ['Dark Templar', 'Paladin Weapon Master', 'Manaforge Smith', 'Hired Eidolon', 'Desert Nomad'],
    $passed_things))) {
    // error_log('Old map submitted');
    die();
}

// PDO
$pdo = new PDO("mysql:host=$mms_servername;port=$mms_port;dbname=$mms_dbname;charset=utf8", $mms_username, $mms_password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$query = $pdo->prepare('SELECT 1 FROM map_records WHERE map_id = :id LIMIT 1');
$query->execute(array('id' => $_POST['id']));

if ($query->fetchColumn()) {
    die();
}

$query = $pdo->prepare('SELECT 1 FROM map_mice WHERE map_id = :id LIMIT 1');
$query->execute(array('id' => $_POST['id']));

if ($query->fetchColumn()) {
    //error_log("Spotter tried to insert existing map mice again for map id $_POST[id]");
    // Not sure why this even gets hit so much
    die();
}

// Get map id
$query = $pdo->prepare('SELECT m.id FROM maps m WHERE m.name LIKE ?');
$query->execute(array($_POST['name']));
$map_type_id = $query->fetchColumn();

if (!$map_type_id) {
    $query = $pdo->prepare('INSERT INTO maps (name) VALUES (?)');
    $query->execute(array($_POST['name']));
    $map_type_id = $pdo->lastInsertId();
}


// Get mice ids
$mice_supplied_count = count($passed_things);
$mice_ids = [];
foreach ($passed_things as $mouse_name) {
    $query = $pdo->prepare("SELECT m.id FROM mice m WHERE m.name LIKE ?");
    $query->execute(array($mouse_name));
    $mouse_id = $query->fetchColumn();

    if (!$mouse_id) {
        $query = $pdo->prepare("INSERT INTO mice (name) VALUES (?)");
        $query->execute(array($mouse_name));
        $mouse_id = $pdo->lastInsertId();
    }
    $mice_ids[] = $mouse_id;
}
if ($mice_supplied_count != count($mice_ids)) {
    error_log("Map intake should have found $mice_supplied_count mice ids, but instead found " . count($mice_ids) . " ids, for map id $_POST[id]");
    die();
}


// Record map with mice
$query = $pdo->prepare('INSERT INTO map_records (map_id, map_type_id, extension_version) VALUES (?, ?, ?)');
$query->execute(array($_POST['id'], $map_type_id, $_POST['extension_version']));

$insert_query = 'INSERT INTO map_mice (map_id, mouse_id) VALUES (:map_id,';
$insert_query .= implode('),(:map_id,', $mice_ids);
$insert_query .= ')';

$query = $pdo->prepare($insert_query);
$query->execute(array('map_id' => $_POST['id']));

$mice_inserted_count = $query->rowCount();
if ($mice_supplied_count != $mice_inserted_count) {
    error_log("Map intake should have inserted $mice_supplied_count mice, but instead inserted $mice_inserted_count, for map id $_POST[id]");
}

sendResponse('success', "Thanks for the map info!");
