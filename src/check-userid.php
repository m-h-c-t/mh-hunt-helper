<?php

require_once "check-direct-access.php";
require_once "config.php";
require_once "db-connect.php";

$user_id;
if (!empty($_REQUEST['hunter_id_hash'])) {
    $query = $pdo->prepare('SELECT u.id FROM users u WHERE u.digest2022 = ?');
    $query->execute(array($_REQUEST['hunter_id_hash']));
    $user_id = $query->fetchColumn();
}
