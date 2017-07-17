<?php

require_once "config.php";

main();

function main() {
    global $pdo, $mms_servername, $mms_dbname, $mms_username, $mms_password;

    // PDO
    $pdo = new PDO("mysql:host=$mms_servername;dbname=$mms_dbname;charset=utf8", $mms_username, $mms_password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

    if (empty($_REQUEST['mh_action'])) {
        error_log('Unknown sniper request mh_action.');
        return;
    }

    switch ($_REQUEST['mh_action']) {
        case 'getAllRequests':
            getAllRequests();
            break;
        case 'getUserRequests':
            getUserRequests();
            break;
        case 'createNewRequest':
            createNewRequest();
            break;
        case 'expireRequest':
            expireRequest();
            break;
    }
}

function getAllRequests() {
    global $pdo;
    $db_values = array();
    if (!empty($_REQUEST['typeFilter'])) {
        $db_values[] = $_REQUEST['typeFilter'];
    }

    $query = getRequestsQueryBuilder();
    $query = $pdo->prepare($query);
    $query->execute($db_values);
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    print json_encode($results);
}

function getUserRequests() {
    global $pdo;

    if (empty($_REQUEST['fbUserId']) || !is_numeric($_REQUEST['fbUserId']) || $_REQUEST['fbUserId'] <= 0  || !verifyFBUser()) {
        die('You are not recognized as a valid fb user. Please contact Jack if this is an error.');
    }

    $query = getRequestsQueryBuilder(true);
    $query = $pdo->prepare($query);
    $query->execute(array($_REQUEST['fbUserId']));
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    print json_encode($results);
}

function getRequestsQueryBuilder($user_specific = false) {
    $query = '
        SELECT r.id, UNIX_TIMESTAMP(r.timestamp) as timestamp, mhmhm.name as mouse, f.first_name, f.fb_id, r.reward_count, r.man_expired, m.name as map, r.dusted, rt.name as request_type
        FROM requests r
        INNER JOIN fb_users f ON r.fb_user_id = f.id
        INNER JOIN request_types rt on r.type_id = rt.id
        LEFT JOIN mhmaphelper.mice mhmhm ON r.mouse_id = mhmhm.id
        LEFT JOIN maps m ON r.map_id = m.id
        WHERE ';

    if (!$user_specific) {
        $query .= 'r.timestamp > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 48 HOUR)) AND r.man_expired = 0';
        if (!empty($_REQUEST['typeFilter'])) {
            $query .= ' AND rt.name LIKE ?';
        }
        $query .= ' ORDER BY r.timestamp ASC';
    } else {
        $query .= 'f.fb_id = ? ORDER BY r.man_expired ASC, r.timestamp DESC';
    }

    return $query;
}

function createNewRequest() {
    global $pdo;
    $error_message = 'Missing some of the info, please make sure to log in and fill out the form completely.';

    if (empty($_REQUEST['postType'])) {
        error_log('Unknown sniper request mh_action.');
        die($error_message);
    }

    $required_fields = [
        'fbUserId'      => 'number',
        'fbAccessToken' => 'text',
        'fName'         => 'text',
        ];

    switch ($_REQUEST['postType']) {
        case 'snipe_request':
        case 'snipe_offer':
            $required_fields['mouseId'] = 'number';
            $required_fields['rewardCount'] = 'number';
            $db_fields = 'mouse_id, reward_count';
            $db_values = array($_POST['mouseId'], $_POST['rewardCount']);
            break;
        case 'leech_request':
        case 'leech_offer':
            $required_fields['mapId'] = 'number';
            $required_fields['rewardCount'] = 'number';
            $db_fields = 'map_id, reward_count';
            $db_values = array($_POST['mapId'], $_POST['rewardCount']);
            if (!empty($_POST['mapDust'])) {
                $db_fields .= ', dusted';
                $db_values[] = $_POST['mapDust'];
            }
            break;
        case 'helper_request':
        case 'helper_offer':
            $required_fields['mapId'] = 'number';
            $db_fields = 'map_id';
            $db_values = array($_POST['mapId']);
            if (!empty($_POST['mapDust'])) {
                $db_fields .= ', dusted';
                $db_values[] = $_POST['mapDust'];
            }
            break;
        default:
            die($error_message);
            break;
    }

    foreach ($required_fields as $name => $type) {
        if (empty($_POST[$name])) {
            die($error_message);
        }

        if ($type === 'text' && strlen($_POST[$name]) <= 0) {
            die($error_message);
        } else if ($type === 'number' && (!is_numeric($_POST[$name]) || $_POST[$name] <=0 )) {
            die($error_message);
        }
    }

    // check if user is banned
    $query = $pdo->prepare("SELECT id, banned FROM fb_users WHERE fb_id = ?");
    $query->execute(array($_REQUEST['fbUserId']));
    $fb_user = $query->fetch();

    if (!empty($fb_user['id']) && $fb_user['banned'] != 0) {
        die('You have been banned. Please contact Jack if you think this is an error.');
    }

    // Check if FB user is valid
    if (!verifyFBUser()) {
        die('You are not recognized as a valid fb user. Please logout of facebook, refresh, and try again.');
    }

    $pdo->beginTransaction();

    if (!empty($fb_user['id'])) {
        // check if user has 3 active posts
        $query = $pdo->prepare("
            SELECT count(*)
            FROM requests r
            WHERE r.fb_user_id = ? AND r.timestamp > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 48 HOUR)) AND r.man_expired = 0");
        $query->execute(array($fb_user['id']));
        $result = $query->fetchColumn();

        if ($result >= 3) {
            die('Users are limited to 3 active posts.');
        }
    }

    // create user if needed
    if (empty($fb_user['id'])) {
        $query = 'INSERT INTO fb_users (fb_id, first_name) VALUES (?, ?)';
        $query = $pdo->prepare($query);
        $query->execute(array($_REQUEST['fbUserId'], $_REQUEST['fName']));
        $fb_user['id'] = $pdo->lastInsertId();
    }

    $placeholders = [];
    foreach ($db_values as $value) {
        $placeholders[] = '?';
    }
    $placeholders = implode(', ', $placeholders);

    $db_fields .= ', fb_user_id, type_id';
    $placeholders .= ', ?, rt.id';
    $db_values = array_merge($db_values, array($fb_user['id'], $_REQUEST['postType']));

    // create the new request
    $query = 'INSERT INTO requests (' . $db_fields . ')
              SELECT ' . $placeholders . '
              FROM mhmapspotter.request_types rt
              WHERE rt.name LIKE ?';
    //error_log(print_r($db_values, true));
    $query = $pdo->prepare($query);
    $query->execute($db_values);
    $request_id = $pdo->lastInsertId();

    $pdo->commit();

    die('Request added!');
}

function expireRequest() {
    global $pdo;

    if (empty($_REQUEST['fbUserId']) || !is_numeric($_REQUEST['fbUserId']) || $_REQUEST['fbUserId'] <= 0 || !verifyFBUser()) {
        die('You are not recognized as a valid fb user. Please contact Jack if this is an error.');
    }
    if (empty($_REQUEST['request_id']) || !is_numeric($_REQUEST['request_id'])) {
        die('Could not find this request, please refresh page and try again.');
    }

    $query = '
        UPDATE requests r
        INNER JOIN fb_users f ON r.fb_user_id = f.id
        SET r.man_expired = 1
        WHERE r.id = ? AND f.fb_id = ? AND r.man_expired = 0';
    $query = $pdo->prepare($query);
    $query->execute(array($_REQUEST['request_id'], $_REQUEST['fbUserId']));

    die('Request expired!');
}

function verifyFBUser() {
    global $fb_app_id, $fb_app_secret;

    if (empty($_REQUEST['fbAccessToken'])) {
        return false;
    }

    $response = file_get_contents("https://graph.facebook.com/debug_token?input_token=$_REQUEST[fbAccessToken]&access_token=$fb_app_id|$fb_app_secret");

    if ($response === false) {
        return false;
    }

    $response = json_decode($response);
    if ($response->data->is_valid === true) {
        return true;
    }

    return false;
}
