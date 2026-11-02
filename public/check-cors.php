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
header("Vary: Origin");

// Preflight: the extension sends X-Requested-With (and sometimes a JSON Content-Type),
// which are not CORS-safelisted, so they must be explicitly allowed.
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept");
    header("Access-Control-Max-Age: 86400");
    die();
}
