<?php

require_once "check-direct-access.php";
require_once "send_response.php";

// Time check
if (empty($_POST['entry_timestamp']) || !is_numeric($_POST['entry_timestamp'])){
    error_log('USER: ' . $_POST['hunter_id_hash'] . " - Entry timestamp is missing");
    sendResponse('error', "Entry timestamp is missing!");
}
if (abs($_SERVER['REQUEST_TIME'] - $_POST['entry_timestamp']) > 30) {
    error_log('USER: ' . $_POST['hunter_id_hash'] . " - Time didn't match: " . $_SERVER['REQUEST_TIME'] . " vs " . $_POST['entry_timestamp']);
    sendResponse('error', "Time didn't match!");
}
