<?php

// For dev env we can just replace all that with few lines:
// $http_origin = $_SERVER['HTTP_ORIGIN'];
// header("Access-Control-Allow-Origin: $http_origin");
// if (!$_REQUEST['uuid']) {
//     echo "1";
// }
// return;

require_once "check-cors.php";

$uuid_timeout = 20;

if (empty($_REQUEST['entry_timestamp'])) {
    error_log("UUID Request Failed, missing entry_timestamp");
    die();
}
else if(empty($_REQUEST['hunter_id_hash'])) {
    error_log("UUID Request Failed, missing user_id");
    die();
}

require_once "config.php";

require __DIR__ . '/../vendor/autoload.php';
use Ramsey\Uuid\Uuid;

$redis = new Predis\Client();

if (defined('not_direct_access')) {
    // Checking and deleting uuid
    $myuuid_time = $redis->get($_REQUEST['uuid']);
    if (!$myuuid_time || abs(time() - $myuuid_time) > $uuid_timeout) {
        error_log("Failed uuid test with hunter_id_hash: " . $_REQUEST['hunter_id_hash'] . " and timestamp: " . $_REQUEST['entry_timestamp']);
        die();
    }
    $redis->del($_REQUEST['uuid']);

} else {
    // Creating uuid
    $myuuid = Uuid::uuid4();
    $myuuid = $myuuid->toString();
    $redis->set($myuuid,time());
    die($myuuid);
}

