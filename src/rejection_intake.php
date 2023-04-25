<?php

if (empty($_POST['pre']) || empty($_POST['post']) || empty($_REQUEST['hunter_id_hash'])) {
    error_log("Missing pre, post, or hunter id hash");
    sendResponse('success', "Thanks for the hunt info!");
    die();
}

define('not_direct_access', TRUE);
require_once "check-cors.php";
require_once "uuid.php";
require_once "check-ban.php";
require_once "check-time.php";
require_once "config.php";
require_once "check-version.php";
require_once "db-connect.php";
require_once "send_response.php";
recordRejectionsInFile($_POST['entry_timestamp'], $_POST['pre'], $_POST['post']);
sendResponse('success', "Thanks for the hunt info!");

die();

function recordRejectionsInFile($timestamp, $preData, $postData, $limit = 250) {
    $file_name = 'rejections.json';

    $data = file_get_contents($file_name);

    if (!empty($data)) {
        $data = json_decode($data, true);

        if (count($data) >= $limit) {
            $data = array_slice($data, -$limit + 1, $limit - 1, true); // Limit to last N - 1 entries
        }
    }

    $data[$timestamp] = getRejectionRecord($timestamp, $preData, $postData);

    file_put_contents($file_name, json_encode($data));
}

function getRejectionRecord($timestamp, $pre, $post) {
    return [
        'date' => date('Y-m-d\TH:i:s', $timestamp),
        'mouse' => $pre['mouse'],
        'pre' => getEnvironmentData($pre),
        'post' => getEnvironmentData($post),
    ];
}

function getEnvironmentData($rejectionData) {
    return [
        'location' => $rejectionData['location'],
        'stage' => $rejectionData['stage'],
    ];
}

?>
