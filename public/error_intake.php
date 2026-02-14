<?php

define('not_direct_access', TRUE);
require_once "check-cors.php";
require_once "uuid.php";
require_once "check-ban.php";
require_once "check-time.php";
require_once "config.php";
require_once "check-version.php";
require_once "send_response.php";

foreach(['issues', 'hunter_id_hash', 'entry_timestamp', 'extension_version'] as $field) {
    if (empty($_POST[$field])) {
        error_log("Missing $field in error_intake.");
        sendResponse('error', "Missing $field in error_intake.");
    }
}

recordErrorsInFile();
sendResponse('success', "Thanks for the hunt info!");

function recordErrorsInFile($limit = 250) {
    $timestamp = $_POST['entry_timestamp'];
    $issues = $_POST['issues'];

    // Validate 'issues' field: ensure type and size are reasonable before logging.
    if (!is_array($issues)) {
        error_log("Invalid type for 'issues' in error_intake. Expected array.");
        sendResponse('error', "Invalid data for 'issues'.");
    }

    // Check the JSON encoded size to ensure it's reasonable
    $issuesJson = json_encode($issues);
    if ($issuesJson === false || json_last_error() !== JSON_ERROR_NONE) {
        error_log("Unencodable 'issues' field received in error_intake.");
        sendResponse('error', "Invalid content in 'issues'.");
    }

    $maxIssuesLength = 10000; // limit size of a single 'issues' entry
    if (strlen($issuesJson) > $maxIssuesLength) {
        error_log("Received oversized 'issues' field in error_intake (length " . strlen($issuesJson) . ").");
        sendResponse('error', "The 'issues' field is too large.");
    }

    $file_name = 'errors.json';

    $data = file_exists($file_name) ? file_get_contents($file_name) : '';

    if (!empty($data)) {
        $decoded = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            error_log("Invalid JSON in {$file_name} in recordErrorsInFile; reinitializing to empty array.");
            $data = [];
        } else {
            $data = $decoded;
            krsort($data, 1); // sort it by timestamp descending

            if (count($data) >= $limit) {
                $data = array_slice($data, 0, $limit - 1, true); // Limit to last limit - 1 entries
            }
        }
    } else {
        $data = [];
    }

    $data[$timestamp] = [
        'date' => date('Y-m-d\TH:i:s', $timestamp),
        'extension_version' => $_POST['extension_version'],
        'issues' => $issues,
        'url' => $_POST['url'] ?? '',
        'message' => $_POST['message'] ?? '',
        'context' => $_POST['context'] ?? '',
    ];
    krsort($data, 1); // sort it by timestamp descending

    $json = json_encode($data);
    if ($json === false) {
        error_log('Failed to encode error data to JSON in recordErrorsInFile: ' . json_last_error_msg());
        return;
    }
    $writeResult = file_put_contents($file_name, $json);
    if ($writeResult === false) {
        error_log("Failed to write error log data to {$file_name} in error_intake.");
        sendResponse('error', 'Failed to record errors.');
    }
}
