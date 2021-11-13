<?php

require_once "check-direct-access.php";

function sendResponse($status, $message) {
    $response = json_encode([
        'status' => $status,
        'message' => $message
    ]);
    die($response);
}

?>