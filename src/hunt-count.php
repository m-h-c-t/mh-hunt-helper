<?php

if (empty($_GET['hunter-id']) || !is_numeric($_GET['hunter-id'])) {
    print "<b>PLEASE SPECIFY A VALID HUNTER ID</b>";
    return;
} else {
    $not_direct_access_id = true;
    $encrypted_user_id = $_GET['hunter-id'];
    require "id_modifier.php";
}

require_once "config.php";
require_once "db-connect.php";

$query = $pdo->prepare('SELECT id FROM users WHERE digest LIKE ?');
$query->execute(array($encrypted_user_id));

$user_id = $query->fetchColumn();

$count_query_string = "SELECT count(*) FROM hunts where user_id = ?";
$query2 = $pdo->prepare($count_query_string);
$query2->execute(array($user_id));
$count = $query2->fetchColumn();

print $count;
