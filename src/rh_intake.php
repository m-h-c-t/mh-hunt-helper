<?php

if (!defined('not_direct_access') && !empty($_POST['hint']) && !empty($_POST['user_id'])) {
    define('not_direct_access', TRUE);
    require_once "check-cors.php";
    require_once "uuid.php";
    require_once "check-time.php";
    require_once "config.php";
    require_once "db-connect.php";
    require_once "send_response.php";
    recordRelicHunterFromHint($_POST['hint']);
    sendResponse('success', "Thanks for reporting RH!");
}

require_once "check-direct-access.php";

// Check if Relic Hunter submission
if (!empty($_POST['rh_environment']) && !empty($_POST['user_id'])) {
    recordRelicHunter();
	sendResponse('success', "Thanks for reporting RH!");
    // Dies in sendResponse
}
// else it's not an RH submission, let it continue

// ------ RH FUNCTIONS -----

function recordRelicHunterFromHint($hint) {
    global $pdo;
    $query = $pdo->prepare('
        SELECT l.name
        FROM rh_hints rh
        INNER JOIN locations l on rh.location_id = l.id
        WHERE rh.hint LIKE ?;');
    $query->execute(array($hint));
    $location = $query->fetchColumn();
    if (!$location) {
        error_log("RH Hint is bad or new.");
        die();
    }
    $location = getAliasLocationName($location);
    recordRelicHunterInFile($location);
    recordRelicHunterInDB($location);
}

function recordRelicHunter() {
    if (empty($_POST['entry_timestamp']) || !is_numeric($_POST['entry_timestamp'])) {
        error_log("RH Submission bad or missing timestamp.");
        die();
    }
    $location = getAliasLocationName(filter_var($_POST['rh_environment'], FILTER_SANITIZE_STRING));
    recordRelicHunterInFile($location);
    recordRelicHunterInDB($location);
}

function recordRelicHunterInDB($location) {
    global $pdo;
    $query = $pdo->prepare('
        INSERT INTO rh_tracker(`date`, location_id)
        SELECT ?, l.id FROM locations l
        WHERE l.name LIKE ?
        ON DUPLICATE KEY UPDATE location_id = l.id;');
    $query->execute(array(date("Y-m-d"), $location));
}

function recordRelicHunterInFile($location) {
    $file_name = 'tracker.json';

    $data = file_get_contents($file_name);

    if (!empty($data)) {
        $data = json_decode($data);
        if (empty($data->rh) || $data->rh->last_seen > $_POST['entry_timestamp'] ) {
            return;
        }
        $data->rh->location = $location;
        $data->rh->last_seen = $_POST['entry_timestamp'];
    } else {
        $data = [
            "rh" => [
                "location" => $location,
                "last_seen" => $_POST['entry_timestamp']
            ]
        ];
    }

    file_put_contents($file_name, json_encode($data));
}

function getAliasLocationName($input) {
    switch ($input) {
        case 'Living Garden':
        case 'Twisted Garden':
            $input = 'Living/Twisted Garden';
            break;

        case 'Sand Dunes':
        case 'Sand Crypts':
            $input = 'Sand Dunes/Crypts';
            break;

        case 'Lost City':
        case 'Cursed City':
            $input = 'Lost/Cursed City';
            break;

        default:
            // No-op
            break;
    }
    return $input;
}
