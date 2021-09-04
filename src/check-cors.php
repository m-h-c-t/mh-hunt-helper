<?php

// CORS check
if (!$_SERVER['HTTP_ORIGIN'] || ($_SERVER['HTTP_ORIGIN'] !== "https://www.mousehuntgame.com" && $_SERVER['HTTP_ORIGIN'] !== "http://www.mousehuntgame.com")) {
    error_log("Origin didn't match, requests origin was: " . $_SERVER['HTTP_ORIGIN'] . ip_display());
    die();
}
header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
#header("X-Content-Type-Options: nosniff");
#header("Content-Type: application/json");
