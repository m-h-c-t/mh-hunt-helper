<?php

require_once "check-direct-access.php";
require_once "config.php";
require_once "db-connect.php";

$user_id;
if (empty($_REQUEST['hunter_id_hash'])) {
    error_log("missing hash for check-userid.php");
    die();
}

$query = $pdo->prepare('SELECT u.id FROM users u WHERE u.digest2022 = ?');
$query->execute(array($_REQUEST['hunter_id_hash']));
$user_id = $query->fetchColumn();

// If can't find by digest2022, try old way and update new hash
if (empty($user_id) && !empty($_REQUEST['user_id'])) {

    $encrypted_user_id = $_REQUEST['user_id'];
    require_once "id_modifier.php";

    $query = $pdo->prepare('SELECT id FROM users WHERE digest LIKE ?');
    $query->execute(array($encrypted_user_id));

    $user_id = $query->fetchColumn();

    // Update new hash if found
    if (!empty($user_id)) {
        $query = $pdo->prepare('UPDATE users set digest2022 = ? WHERE id = ? and digest2022 is NULL');
        $query->execute(array($_REQUEST['hunter_id_hash'], $user_id));
    }
}

// If still can't find, create new user
if (empty($user_id)) {
    $query = $pdo->prepare('INSERT INTO users (digest2022) VALUES (?)');
    $query->execute(array($_REQUEST['hunter_id_hash']));
    $user_id = $pdo->lastInsertId();
}
