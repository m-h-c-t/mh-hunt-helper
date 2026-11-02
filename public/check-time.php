<?php

require_once "check-direct-access.php";
require_once "send_response.php";

// Time check
if (empty($_POST['entry_timestamp']) || !is_numeric($_POST['entry_timestamp'])){
    error_log('USER: ' . $_POST['hunter_id_hash'] . " - Entry timestamp is missing");
    sendResponse('error', "Entry timestamp is missing!");
}
$age = $_SERVER['REQUEST_TIME'] - $_POST['entry_timestamp'];
$is_linked = isset($_POST['hunt_details']['hunt_type']) && $_POST['hunt_details']['hunt_type'] === 'linked';
$time_ok = $is_linked ? ($age >= -30 && $age <= 600) : abs($age) <= 30;
if (!$time_ok) {
    error_log('USER: ' . $_POST['hunter_id_hash'] . " - Time didn't match" . ($is_linked ? ' (linked)' : '') . ": " . $_SERVER['REQUEST_TIME'] . " vs " . $_POST['entry_timestamp']);
    sendResponse('error', "Time didn't match!");
}
