<?php

require_once "check-direct-access.php";

// Check if Relic Hunter submission
if (!empty($_POST['rh_environment']) && !empty($_POST['user_id'])) {
    recordRelicHunter();
	sendResponse('success', "Thanks for reporting RH!");
    // Dies in sendResponse
}
// else it's not an RH submission

function recordRelicHunter() {
    if (empty($_POST['entry_timestamp']) || !is_numeric($_POST['entry_timestamp'])) {
        error_log("RH Submission bad or missing timestamp.");
        die();
    }
    recordRelicHunterInFile();
    recordRelicHunterInDB();
}

function recordRelicHunterInDB() {
    global $pdo;
    $query = $pdo->prepare('INSERT IGNORE INTO rh_tracker(`date`, location_id) SELECT ?, l.id FROM locations l WHERE l.name LIKE ?;');
    $query->execute(array(date("Y-m-d"), $_POST['rh_environment']));
}

function recordRelicHunterInFile() {
    $file_name = 'tracker.json';
    $location = getAliasLocationName(filter_var($_POST['rh_environment'], FILTER_SANITIZE_STRING));

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
