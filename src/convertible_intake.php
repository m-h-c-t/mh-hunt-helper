<?php
define('not_direct_access', TRUE);
require_once "send_response.php";
require_once "check-ban.php";
require_once "check-cors.php";
require_once "config.php";
require_once "check-version.php";
require_once "db-connect.php";
require_once "check-userid.php";


$required_fields = [
    'asset_package_hash' => 'string',
    'items'              => 'array',
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

$convertible_fields = [
    'name'     => 'string',
    'id'       => 'number',
    'quantity' => 'number',
];

foreach ($convertible_fields as $field => $type) {
    if (empty($_POST['convertible'][$field])) {
        error_log("['convertible'] $field field is missing");
        die();
    }
    if ($type == 'number' && !is_numeric($_POST['convertible'][$field])) {
        error_log("['convertible'] $field field is not numeric");
        die();
    }
}

// PDO
setPDOConv();

// Check for existing entry
// $query = $pdo_conv->prepare('SELECT 1 FROM entries WHERE user_id = :user_id AND asset_hash = :asset_hash LIMIT 1');
// $query->execute(array('user_id' => $_POST['user_id'], 'asset_hash' => $_POST['asset_package_hash']));

// if ($query->fetchColumn()) {
    // error_log("Converter tried to insert existing asset for user id $_POST[user_id]");
    // die();
// }

// Record new entry
$query = $pdo_conv->prepare('INSERT INTO entries (user_id, asset_hash, extension_version) VALUES (?, ?, ?)');
$query->execute(array($user_id, $_POST['asset_package_hash'], $_POST['extension_version']));
$entry_id = $pdo_conv->lastInsertId();

// Check for existing convertible
$query = $pdo_conv->prepare('SELECT c.id FROM convertibles c WHERE c.id = ? LIMIT 1');
$query->execute(array($_POST['convertible']['id']));

// Record new convertible
if (!$query->fetchColumn()) {
    $query = $pdo_conv->prepare('INSERT INTO convertibles (id, name) VALUES (?, ?)');
    $query->execute(array($_POST['convertible']['id'], $_POST['convertible']['name']));
}

// Loop through items
foreach ($_POST['items'] as $item) {
    // Check for existing item
    $query = $pdo_conv->prepare('SELECT i.id FROM items i WHERE i.id = ? LIMIT 1');
    $query->execute(array($item['id']));

    // Record new item
    if (!$query->fetchColumn()) {
        $query = $pdo_conv->prepare('INSERT INTO items (id, name) VALUES (?, ?)');
        $query->execute(array($item['id'], $item['name']));
    }

    // Record new convertible-item
    $query = $pdo_conv->prepare('INSERT INTO convertible_item (entry_id, convertible_id, convertible_quantity, item_id, item_quantity) VALUES (?, ?, ?, ?, ?)');
    $query->execute(array($entry_id, $_POST['convertible']['id'], $_POST['convertible']['quantity'], $item['id'], $item['quantity']));
}

sendResponse('success', "Thanks for the convertible info!");
