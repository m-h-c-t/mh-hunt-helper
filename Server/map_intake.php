<?php
define('not_direct_access', TRUE);
require_once "bcheck.php";

$http_origin = $_SERVER['HTTP_ORIGIN'];

if ($http_origin !== "https://www.mousehuntgame.com" && $http_origin !== "http://www.mousehuntgame.com") {
    error_log("Origin didn't match, requests origin was: " . $http_origin);
    die();
}

header("Access-Control-Allow-Origin: $http_origin");

if (
    empty($_POST['mice'])              ||
    empty($_POST['id'])                || !is_numeric($_POST['id']) ||
    empty($_POST['name'])              ||
    empty($_POST['extension_version']) || !is_numeric($_POST['extension_version'])
    ) {
    error_log("One of the fields was missing");
    die();
}

require_once "config.php";

if (!in_array($_POST['extension_version'], $allowed_extension_versions)) {
    error_log("Bad version: " . $_POST['extension_version']);
    sendResponse('error', "Please update extension to the latest version.");
}

if ($_POST['name'] == 'Arduous Chrome Map' && (in_array('Dark Templar', $_POST['mice'])
    || in_array('Paladin Weapon Master', $_POST['mice'])
    || in_array('Manaforge Smith', $_POST['mice'])
    || in_array('Hired Eidolon', $_POST['mice'])
    || in_array('Desert Nomad', $_POST['mice']))) {
    // error_log('Old map submitted');
    die();
}

// PDO
$pdo = new PDO("mysql:host=$mms_servername;dbname=$mms_dbname;charset=utf8", $mms_username, $mms_password);
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

$query = $pdo->prepare('SELECT m.id FROM maps m WHERE m.name LIKE ? LIMIT 1');
$query->execute(array($_POST['name']));
$map_type_id = $query->fetchColumn();

if (!$map_type_id) {
    $query = $pdo->prepare('INSERT INTO maps (name) VALUES (?)');
    $query->execute(array($_POST['name']));
    $map_type_id = $pdo->lastInsertId();
}

$query = $pdo->prepare('INSERT INTO map_records (map_id, map_type_id) VALUES (?, ?)');
$query->execute(array($_POST['id'], $map_type_id));

$mice = implode('|', $_POST['mice']);
$mice = '^(' . $mice . ')$';

$query = $pdo->prepare("
    INSERT INTO map_mice (map_id, mouse_id)
    SELECT DISTINCT ?, m.id
    FROM $mhmh_dbname.mice m
    WHERE m.name REGEXP ?");
$query->execute(array($_POST['id'], $mice));

$mice_inserted_count = $query->rowCount();
$mice_supplied_count = count($_POST['mice']);
if ($mice_supplied_count != $mice_inserted_count) {
    error_log("Spotter should have inserted $mice_supplied_count mice, but instead inserted $mice_inserted_count, for map id $_POST[id]");
}

sendResponse('success', "Thanks for the map info!");

function sendResponse($status, $message) {
	$response = json_encode([
		'status' => $status,
		'message' => $message
	]);
	die($response);
}
