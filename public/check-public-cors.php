<?php
// Set CORS policy on public API endpoints

$public_cors_whitelist = [
    'https://www.mousehuntgame.com',
    'https://markethunt.win',
    'https://dev.markethunt.win'
];

if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    if (!in_array($_SERVER['HTTP_ORIGIN'], $public_cors_whitelist)) {
        error_log("Origin didn't match, requests origin was: " . $_SERVER['HTTP_ORIGIN']);
        die();
    } else {
        header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    die();
}
