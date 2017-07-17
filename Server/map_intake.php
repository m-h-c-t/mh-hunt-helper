<?php
$http_origin = $_SERVER['HTTP_ORIGIN'];

if ($http_origin !== "https://www.mousehuntgame.com" && $http_origin !== "http://www.mousehuntgame.com") {
    error_log("Origin didn't match, requests origin was: " . $http_origin);
    thanks();
}

header("Access-Control-Allow-Origin: $http_origin");

if (
    empty($_POST['mice'])              ||
    empty($_POST['id'])                || !is_numeric($_POST['id']) ||
    empty($_POST['name'])              ||
    empty($_POST['extension_version']) || !is_numeric($_POST['extension_version'])
    ) {
    error_log("One of the fields was missing");
    thanks();
}

if (!in_array($_POST['extension_version'], [11108, 11109])) {
    error_log("Bad version: " . $_POST['extension_version']);
    die("MH Helper: Please update to the latest version!");
}

require "config.php";

// PDO
$pdo = new PDO("mysql:host=$mms_servername;dbname=$mms_dbname;charset=utf8", $mms_username, $mms_password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$query = $pdo->prepare('SELECT 1 FROM map_records WHERE map_id = :id LIMIT 1');
$query->execute(array('id' => $_POST['id']));

if ($query->fetchColumn()) {
    thanks();
}

$query = $pdo->prepare('
    INSERT INTO map_records (map_id, map_type_id)
    SELECT ?, m.id
    FROM maps m
    WHERE m.name LIKE ?');
$query->execute(array($_POST['id'], $_POST['name']));

$mice = implode('|', $_POST['mice']);
$mice = '^(' . $mice . ')$';

$query = $pdo->prepare("
    INSERT INTO map_mice (map_id, mouse_id)
    SELECT ?, m.id
    FROM $mhmh_dbname.mice m
    WHERE m.name REGEXP ?");
$query->execute(array($_POST['id'], $mice));

thanks();

function thanks() {
    die("MH Helper: Thanks for the map info!");
}
