<?php

require_once "check-direct-access.php";

function sendResponse($status, $message) {
    $dev_mode = false; // Set to true for development mode
    header('Content-Type: application/json');
    $response = $dev_mode ? json_encode([
            'status' => $status,
            'message' => $message
        ]) : json_encode([
            'status' => 'success',
            'message' => 'Thanks for the hunt info!',
    ]);

    die($response);
}
