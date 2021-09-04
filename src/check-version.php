<?php

// Extension version check
if (!in_array($_POST['extension_version'], $allowed_extension_versions)) {
    //    error_log("User $_POST[user_id]: Bad version: " . $_POST['extension_version'] . ip_display());
    sendResponse('error', "Please update extension to the latest version.");
}
