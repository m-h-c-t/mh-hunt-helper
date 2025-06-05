<?php

require_once "check-direct-access.php";

function sendResponse($status, $message) {
    header('Content-Type: application/json');
    $response = json_encode([
        'status' => $status,
        'message' => $message
    ]);
    die($response);
}
