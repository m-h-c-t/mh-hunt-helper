<?php
define('not_direct_access', TRUE);
require_once "check-ban.php";

$http_origin = $_SERVER['HTTP_ORIGIN'];

if ($http_origin !== "https://www.mousehuntgame.com" && $http_origin !== "http://www.mousehuntgame.com") {
    error_log("Origin didn't match, requests origin was: " . $http_origin);
    die();
}

header("Access-Control-Allow-Origin: $http_origin");
// header("X-Content-Type-Options: nosniff");
// header("Content-Type: application/json");

require_once "config.php";

if (
    empty($_POST['asset_package_hash'])      ||
    empty($_POST['convertible']['name'])     ||
    empty($_POST['convertible']['id'])       || !is_numeric($_POST['convertible']['id'])       ||
    empty($_POST['convertible']['quantity']) || !is_numeric($_POST['convertible']['quantity']) ||
    empty($_POST['user_id'])                 || !is_numeric($_POST['user_id'])                 ||
    empty($_POST['items'])                   ||
    empty($_POST['extension_version'])       || !is_numeric($_POST['extension_version'])
) {
    error_log("One of the fields was missing");
    die();
}

if (!in_array($_POST['extension_version'], $allowed_extension_versions)) {
    error_log("Bad version: " . $_POST['extension_version']);
    sendResponse('error', "Please update extension to the latest version.");
}

// PDO
$pdo = new PDO("mysql:host=$convertible_servername;port=$convertible_port;dbname=$convertible_dbname;charset=utf8", $convertible_username, $convertible_password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

// Check for existing entry
// $query = $pdo->prepare('SELECT 1 FROM entries WHERE user_id = :user_id AND asset_hash = :asset_hash LIMIT 1');
// $query->execute(array('user_id' => $_POST['user_id'], 'asset_hash' => $_POST['asset_package_hash']));

// if ($query->fetchColumn()) {
    // error_log("Converter tried to insert existing asset for user id $_POST[user_id]");
    // die();
// }

// Record new entry
$query = $pdo->prepare('INSERT INTO entries (user_id, asset_hash, extension_version) VALUES (?, ?, ?)');
$query->execute(array($_POST['user_id'], $_POST['asset_package_hash'], $_POST['extension_version']));
$entry_id = $pdo->lastInsertId();

// Check for existing convertible
$query = $pdo->prepare('SELECT c.id FROM convertibles c WHERE c.id = ? LIMIT 1');
$query->execute(array($_POST['convertible']['id']));

// Record new convertible
if (!$query->fetchColumn()) {
    $query = $pdo->prepare('INSERT INTO convertibles (id, name) VALUES (?, ?)');
    $query->execute(array($_POST['convertible']['id'], $_POST['convertible']['name']));
}

// Loop through items
foreach ($_POST['items'] as $item) {
    // Check for existing item
    $query = $pdo->prepare('SELECT i.id FROM items i WHERE i.id = ? LIMIT 1');
    $query->execute(array($item['id']));

    // Record new item
    if (!$query->fetchColumn()) {
        $query = $pdo->prepare('INSERT INTO items (id, name) VALUES (?, ?)');
        $query->execute(array($item['id'], $item['name']));
    }

    // Record new convertible-item
    $query = $pdo->prepare('INSERT INTO convertible_item (entry_id, convertible_id, convertible_quantity, item_id, item_quantity) VALUES (?, ?, ?, ?, ?)');
    $query->execute(array($entry_id, $_POST['convertible']['id'], $_POST['convertible']['quantity'], $item['id'], $item['quantity']));
}

sendResponse('success', "Thanks for the convertible info!");

function sendResponse($status, $message) {
    $response = json_encode([
        'status' => $status,
        'message' => $message
    ]);
    die($response);
}
