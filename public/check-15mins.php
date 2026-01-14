<?php

require_once "check-direct-access.php";
require_once "config.php";
require_once "db-connect.php";
require_once "check-userid.php";

// 15 minutes between hunts check
$query = $pdo->prepare('SELECT timestamp, entry_id, charm_id FROM hunts WHERE user_id = :user_id ORDER BY timestamp DESC LIMIT 1');
$query->execute(array('user_id' => $user_id));
$row = $query->fetch(PDO::FETCH_ASSOC);

// Also checks for hunter rewind charm
if (!empty($row['timestamp']) && ($_POST['entry_timestamp'] - $row['timestamp']) < 899
    && !empty($row['charm_id']) && $row['charm_id'] != 2584) {
    error_log("User $_POST[hunter_id_hash]: Tried to submit a hunt faster than 15 minutes=============");
	recordOffenders();
    sendResponse('error', "You are submitting hunts too fast!");
}
