<?php

// Can't require this with current uuid.php usage of direct access var
// require_once "check-direct-access.php";

// CORS check
$intake_cors_whitelist = [
    'https://www.mousehuntgame.com',
    'http://www.mousehuntgame.com'
];

if (!$_SERVER['HTTP_ORIGIN'] || !in_array($_SERVER['HTTP_ORIGIN'], $intake_cors_whitelist)) {
    error_log("Origin didn't match, requests origin was: " . $_SERVER['HTTP_ORIGIN']);
    die();
}

header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
#header("X-Content-Type-Options: nosniff");
#header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    die();
}
