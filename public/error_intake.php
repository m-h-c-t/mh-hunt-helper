<?php

define('not_direct_access', TRUE);
require_once "check-cors.php";
require_once "uuid.php";
require_once "check-ban.php";
require_once "check-time.php";
require_once "config.php";
require_once "check-version.php";
require_once "send_response.php";


if (empty($_POST['message'])) {
    error_log("Missing message in error_intake.");
    sendResponse('error', "Missing message in error_intake.");
}

recordErrorsInFile();
sendResponse('success', "Thanks for the hunt info!");

function recordErrorsInFile($limit = 250) {
    $timestamp = $_POST['entry_timestamp'];
    $message = $_POST['message'];

    // decode message if it's JSON encoded, otherwise use it as is
    if (is_string($message)) {
        $decodedMessage = json_decode($message, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $message = $decodedMessage;
        }
    }

    // Check the JSON encoded size to ensure it's reasonable
    $messageJson = json_encode($message);
    if ($messageJson === false || json_last_error() !== JSON_ERROR_NONE) {
        error_log("Unencodable 'message' field received in error_intake.");
        sendResponse('error', "Invalid content in 'message'.");
    }

    $maxMessageLength = 10000; // limit size of a single 'message' entry
    if (strlen($messageJson) > $maxMessageLength) {
        error_log("Received oversized 'message' field in error_intake (length " . strlen($messageJson) . ").");
        sendResponse('error', "The 'message' field is too large.");
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
        'issues' => $decodedMessage,
        'url' => $_POST['url'] ?? '',
        'message' => $messageJson ?? '',
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
