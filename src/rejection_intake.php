<?php

if (!defined('not_direct_access') && !empty($_POST['pre']) && !empty($_POST['post']) && !empty($_REQUEST['hunter_id_hash'])) {
    define('not_direct_access', TRUE);
    require_once "check-cors.php";
    require_once "uuid.php";
    require_once "check-ban.php";
    require_once "check-time.php";
    require_once "config.php";
    require_once "db-connect.php";
    require_once "send_response.php";
    recordRejectionsInFile($_POST['pre'], $_POST['post']);
    sendResponse('success', "Thanks for the hunt info!");
}

sendResponse('success', "Thanks for the hunt info!");

function recordRejectionsInFile($preData, $postData, $limit = 250) {
    $file_name = 'rejections.json';

    $data = file_get_contents($file_name);

    if (!empty($data)) {
        $data = json_decode($data, true);

        if (count($data) >= $limit) {
            $data = array_slice($data, -$limit + 1, $limit - 1, true); // Limit to last N - 1 entries
        }

        $data[$_POST['entry_timestamp']] = [
            'mouse' => $preData['mouse'],
            'pre' => extractRejectionData($preData),
            'post' => extractRejectionData($postData),
        ];

    } else {
        $data = [
            $_POST['entry_timestamp'] => [
                'mouse' => $preData['mouse'],
                'pre' => extractRejectionData($preData),
                'post' => extractRejectionData($postData),
            ]
        ];
    }

    file_put_contents($file_name, json_encode($data));
}

function extractRejectionData($rejectionData) {
    return [
        'location' => $rejectionData['location'],
        'stage' => $rejectionData['stage'],
    ];
}

?>
