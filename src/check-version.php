<?php

require_once "check-direct-access.php";
require_once "config.php";

// Extension version check
if (!in_array($_POST['extension_version'], $allowed_extension_versions)) {
    error_log("User " . $_POST['hunter_id_hash'] . ": Bad version: " . $_POST['extension_version']);
    sendResponse('error', "Please update extension to the latest version.");
}
