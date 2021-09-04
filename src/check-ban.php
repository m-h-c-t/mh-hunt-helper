<?php
if (!defined('not_direct_access')) {
    error_log("bcheck direct access error: " . ip_display());
	header("HTTP/1.0 404 Not Found");
    die();
}

$data = file_get_contents("banned.json");

if (!empty($data)) {
	$data = json_decode($data);

	// Banned IPs
	$banned_ips = $data->ips;
	foreach(['REMOTE_ADDR', 'HTTP_X_FORWARDED_FOR', 'HTTP_CF_CONNECTING_IP'] as $ip) {
		if (!empty($_SERVER[$ip]) && property_exists($banned_ips, $_SERVER[$ip]) && $banned_ips->{$_SERVER[$ip]} > 1)
		{
//			error_log("Banned IP: " . ip_display());
			sendResponse('success', "Thanks for the hunt info!");
		}
	}

	// Banned Users
	$banned_users = $data->user_ids;
	if (!empty($_POST['user_id']) && property_exists($banned_users, $_POST['user_id']) && $banned_users->{$_POST['user_id']} > 1) {
		error_log("Banned user: " . $_POST['user_id'] . ip_display());
		sendResponse('success', "Thanks for the hunt info!");
	}
}

function ip_display() {
	$ips = " IPs: ";
	if (!empty($_SERVER['REMOTE_ADDR'])) {
		$ips .= "Remote_addr: " . $_SERVER['REMOTE_ADDR'] . ". ";
	}
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ips .= "Forwarded_for: " . $_SERVER['HTTP_X_FORWARDED_FOR'] . ". ";
	}
	if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
		$ips .= "CF IP: " . $_SERVER['HTTP_CF_CONNECTING_IP'] . ". ";
	}
	return $ips;
}

function recordOffenders() {
	$file_name = 'banned.json';
	$data = file_get_contents($file_name);
	$user_id;
	
	$cf_ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && !empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
		$cf_ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	}

	if (isset($_POST['user_id'])) {
		$user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
	}
	if (empty($user_id)) {
		$user_id = 0;
	}
	
    if (empty($data)) {
		$data = (object)[
            "ips" => (object)[],
			"user_ids" => (object)[]
        ];
	} else {
	    $data = json_decode($data);
	}

	$data->ips->$cf_ip = isset($data->ips->$cf_ip) ? $data->ips->$cf_ip + 1 : 1;

	if (!empty($user_id)) {
		$data->user_ids->$user_id = isset($data->ips->$user_id) ? $data->ips->$user_id + 1 : 1;
	}

    file_put_contents($file_name, json_encode($data));
}
