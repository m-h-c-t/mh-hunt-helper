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
    $query = getRequestsQueryBuilder();
    $query = $pdo->prepare($query);
    $query->execute();
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
        SELECT r.id, TIMESTAMPDIFF(SECOND, CURRENT_TIMESTAMP(), DATE_ADD(r.timestamp, INTERVAL 72 HOUR)) as timediff, r.timestamp, m.name as mouse, f.first_name, f.fb_link, r.reward_count, r.man_expired
        FROM requests r
        INNER JOIN mhmaphelper.mice m ON r.mouse_id = m.id
        LEFT JOIN requests_fb_users rf ON r.id = rf.request_id
        LEFT JOIN fb_users f ON rf.fb_user_id = f.id
        WHERE ';

    if (!$user_specific) {
        $query .= 'r.timestamp > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 72 HOUR)) AND r.man_expired = 0';
    } else {
        $query .= 'f.fb_id = ?';
    }

    $query .= ' ORDER BY r.man_expired ASC, r.timestamp DESC';

    return $query;
}

function createNewRequest() {
    global $pdo;
    $error_message = 'Missing some of the info, please make sure to log in and fill out the form completely.';

    $required_fields = [
        'mouseId'       => 'number',
        'fbUserId'      => 'number',
        'fbLink'        => 'text',
        'fbAccessToken' => 'text',
        //'mouseName'     => 'text',
        'fName'         => 'text',
        'rewardCount'  => 'number'
        ];

    foreach ($required_fields as $name => $type) {
        if (empty($_POST[$name])) {
            die($error_message);
        }

        if ($type === 'text' && strlen($_POST[$name]) <= 0) {
            return $error_message;
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
        die('You are not recognized as a valid fb user. Please contact Jack if this is an error.');
    }

    $pdo->beginTransaction();

    if (!empty($fb_user['id'])) {
        // check if user made 3 requests within last 15 minutes
        $query = $pdo->prepare("
            SELECT count(*)
            FROM requests r
            INNER JOIN requests_fb_users rf ON r.id = rf.request_id
            WHERE rf.fb_user_id = ? AND r.timestamp > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 15 MINUTE))");
        $query->execute(array($fb_user['id']));
        $result = $query->fetchColumn();

        if ($result >= 3) {
            die('You have made 3 requests within the last 15 minutes. Please wait a while before requesting again.');
        }

        // check for duplicate active request
        $query = $pdo->prepare("
            SELECT count(*)
            FROM requests r
            INNER JOIN requests_fb_users rf ON r.id = rf.request_id
            INNER JOIN fb_users f ON rf.fb_user_id = f.id
            WHERE f.fb_id = ?
                AND r.timestamp > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 72 HOUR))
                AND r.mouse_id = ?
                AND r.man_expired = 0");
        $query->execute(array($fb_user['id'], $_REQUEST['mouseId']));
        $result = $query->fetchColumn();
    }

    // create the new request
    $query = 'INSERT INTO requests (mouse_id, reward_count) VALUES (?, ?)';
    $query = $pdo->prepare($query);
    $query->execute(array($_REQUEST['mouseId'], $_REQUEST['rewardCount']));
    $request_id = $pdo->lastInsertId();

    // create user if needed
    if (empty($fb_user['id'])) {
        $query = 'INSERT INTO fb_users (fb_id, fb_link, first_name) VALUES (?, ?, ?)';
        $query = $pdo->prepare($query);
        $query->execute(array($_REQUEST['fbUserId'], $_REQUEST['fbLink'], $_REQUEST['fName']));
        $fb_user['id'] = $pdo->lastInsertId();
    }

    // create a request-user link
    $query = 'INSERT INTO requests_fb_users (fb_user_id, request_id) VALUES (?, ?)';
    $query = $pdo->prepare($query);
    $query->execute(array($fb_user['id'], $request_id));


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
        INNER JOIN requests_fb_users rf ON r.id = rf.request_id
        INNER JOIN fb_users f ON rf.fb_user_id = f.id
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
