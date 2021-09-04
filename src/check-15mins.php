<?php

require "check-direct-access.php";

if (empty($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
    error_log("User ID is missing");
    sendResponse('success', "Thanks for the hunt info!");
} else {
//        sendResponse('success', "Thanks for the hunt info!");
    $encrypted_user_id = $_POST['user_id'];
    $not_direct_access_id = true;
    require "id_modifier.php";
}

// 15 minutes between hunts check
$query = $pdo->prepare('SELECT id FROM users WHERE digest LIKE ?');
$query->execute(array($encrypted_user_id));

$user_id = $query->fetchColumn();

if (!$user_id) {
    $user_id = 0;
}

$query = $pdo->prepare('SELECT timestamp, entry_id, charm_id FROM hunts WHERE user_id = :user_id ORDER BY timestamp DESC LIMIT 1');
$query->execute(array('user_id' => $user_id));
$row = $query->fetch(PDO::FETCH_ASSOC);

// Also checks for hunter rewind charm
if (!empty($row['timestamp']) && ($_POST['entry_timestamp'] - $row['timestamp']) < 899
    && !empty($row['charm_id']) && $row['charm_id'] != 2584) {
    error_log("User $_POST[user_id]: Tried to submit a hunt faster than 15 minutes=============" . ip_display());
	recordOffenders();
    sendResponse('success', "Thanks for the hunt info!");
}
