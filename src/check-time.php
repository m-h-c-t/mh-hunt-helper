<?php

require_once "check-direct-access.php";

// Time check
if (empty($_POST['entry_timestamp']) || !is_numeric($_POST['entry_timestamp'])){
    error_log("Entry timestamp is missing");
    error_log('USER: ' . $_POST['user_id']);
    sendResponse('success', "Thanks for the hunt info!");
}
if (abs($_SERVER['REQUEST_TIME'] - $_POST['entry_timestamp']) > 30) {
    error_log("Time didn't match: " . $_SERVER['REQUEST_TIME'] . " vs " . $_POST['entry_timestamp'] . ip_display());
    sendResponse('success', "Thanks for the hunt info!");
}
