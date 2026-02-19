<?php

define('not_direct_access', TRUE);
require_once "check-cors.php";
require_once "uuid.php";
require_once "check-ban.php";
require_once "check-time.php";
require_once "config.php";
require_once "check-version.php";
require_once "send_response.php";

if (!isset($_POST['issues']) || !isset($_POST['url']) || !isset($_POST['context'])) {
    error_log("Missing required field in error_intake.");
    sendResponse('error', "Missing required field in error_intake.");
}

recordErrorsInFile();
sendResponse('success', "Thanks for the hunt info!");

function recordErrorsInFile($limit = 250) {
    $timestamp = $_POST['entry_timestamp'];
    $issuesRaw = $_POST['issues'];
    $contextRaw = $_POST['context'];
    $url = $_POST['url'];

    // Decode issues from JSON
    $issues = json_decode($issuesRaw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Invalid JSON in 'issues' field received in error_intake.");
        sendResponse('error', "Invalid JSON in 'issues'.");
    }

    $context = json_decode($contextRaw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Invalid JSON in 'context' field received in error_intake.");
        sendResponse('error', "Invalid JSON in 'context'.");
    }

    $maxJsonLength = 10000; // limit size of a single 'issues' or 'context' entry
    if (strlen($issuesRaw) > $maxJsonLength || strlen($contextRaw) > $maxJsonLength) {
        error_log("Received oversized 'issues' or 'context' field in error_intake (length " . strlen($issuesRaw) . " / " . strlen($contextRaw) . ").");
        sendResponse('error', "The 'issues' or 'context' field is too large.");
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
        'issues' => $issues,
        'context' => $context ?? '',
        'url' => $url ?? '',
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
