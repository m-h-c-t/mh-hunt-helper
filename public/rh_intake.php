<?php

const RH_HINT_CONFIRMATION_COUNT = 5;
const RH_HINT_CONFIRMATION_WINDOW_HOURS = 24;

if (!defined('not_direct_access') && !empty($_POST['hint']) && !empty($_REQUEST['hunter_id_hash'])) {
    define('not_direct_access', TRUE);
    require_once "check-cors.php";
    require_once "uuid.php";
    require_once "check-ban.php";
    require_once "check-time.php";
    require_once "config.php";
    require_once "db-connect.php";
    require_once "send_response.php";
    recordRelicHunterFromHint($_POST['hint']);
    sendResponse('success', "Thanks for reporting RH!");
}

require_once "check-direct-access.php";

// Check if Relic Hunter submission
if (!empty($_POST['rh_environment']) && !empty($user_id)) {
    recordRelicHunter();
    sendResponse('success', "Thanks for reporting RH!");
}

// ------ RH FUNCTIONS -----

function recordRelicHunterFromHint($hint) {
    global $pdo;

    $entryTimestamp = (int) $_POST['entry_timestamp'];

    $query = $pdo->prepare('
        SELECT l.name
        FROM rh_hints rh
        INNER JOIN locations l on rh.location_id = l.id
        WHERE rh.hint = ?;');
    $query->execute([$hint]);
    $location = $query->fetchColumn();

    if (!$location) {
        // If the hint is not already confirmed, record the submission and check if it can be confirmed
        $pdo->beginTransaction();
        try {
            $hunterIdHash = $_REQUEST['hunter_id_hash'];
            $cutoffTimestamp = $entryTimestamp - RH_HINT_CONFIRMATION_WINDOW_HOURS * 60 * 60;

            $query = $pdo->prepare('
                INSERT INTO rh_hint_submissions (hint, hunter_id_hash, entry_timestamp)
                VALUES (?, ?, ?);');
            $query->execute([$hint, $hunterIdHash, $entryTimestamp]);

            $query = $pdo->prepare('
                SELECT COUNT(DISTINCT hunter_id_hash)
                FROM rh_hint_submissions
                WHERE hint = ?
                AND entry_timestamp BETWEEN ? AND ?;');
            $query->execute([$hint, $cutoffTimestamp, $entryTimestamp]);
            $confirmationCount = (int) $query->fetchColumn();

            if ($confirmationCount < RH_HINT_CONFIRMATION_COUNT) {
                $pdo->commit();
                return;
            }

            // We have enough confirmations, so we record it in the rh_hints table
            $query = $pdo->prepare('
                SELECT l.name
                FROM rh_tracker t
                INNER JOIN locations l ON t.location_id = l.id
                WHERE t.`date` = ?
                LIMIT 1;');
            $query->execute([gmdate('Y-m-d', $entryTimestamp)]);
            $location = $query->fetchColumn();

            if (!$location) {
                $pdo->commit();
                error_log("RH Hint confirmed but no UTC-day location is available.");
                return;
            }

            $query = $pdo->prepare('
                INSERT INTO rh_hints (hint, location_id)
                SELECT ?, l.id
                FROM locations l
                WHERE l.name = ?
                ON DUPLICATE KEY UPDATE hint = VALUES(hint);');
            $query->execute([$hint, $location]);
            $pdo->commit();
        } catch (Exception $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }

    $location = getAliasLocationName($location);
    recordRelicHunterInFile($location, $entryTimestamp);
    recordRelicHunterInDB($location, $entryTimestamp);
}

function recordRelicHunter() {
    if (empty($_POST['entry_timestamp']) || !is_numeric($_POST['entry_timestamp'])) {
        error_log("RH Submission bad or missing timestamp.");
        return;
    }
    $location = getAliasLocationName(filter_var($_POST['rh_environment'], FILTER_SANITIZE_STRING));
    recordRelicHunterInFile($location, $_POST['entry_timestamp']);
    recordRelicHunterInDB($location, $_POST['entry_timestamp']);
}

function recordRelicHunterInDB($location, $entryTimestamp) {
    global $pdo;
    $query = $pdo->prepare('
        INSERT INTO rh_tracker(`date`, location_id)
        SELECT ?, l.id FROM locations l
        WHERE l.name LIKE ?
        ON DUPLICATE KEY UPDATE location_id = l.id;');
    $query->execute([gmdate("Y-m-d", $entryTimestamp), $location]);
}

function recordRelicHunterInFile($location, $entryTimestamp) {
    $file_name = 'tracker.json';

    $data = file_get_contents($file_name);

    if (!empty($data)) {
        $data = json_decode($data);
        if (empty($data->rh) || $data->rh->last_seen > $entryTimestamp ) {
            return;
        }
        $data->rh->location = $location;
        $data->rh->last_seen = $entryTimestamp;
    } else {
        $data = [
            "rh" => [
                "location" => $location,
                "last_seen" => $entryTimestamp
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
