<?php

require "check-direct-access.php";

// Giveaway
if ($_POST['entry_timestamp'] >= $giveaway_start_time && $_POST['entry_timestamp'] <= $giveaway_end_time) {
    recordGiveawayHunt($user_id, $_POST['entry_timestamp']);
}

function recordGiveawayHunt($hunter_id_hash = '', $timestamp = '') {
    global $giveaway_url, $giveaway_key;
    $url = $giveaway_url;
    $url .= '?user=' . $hunter_id_hash . '&ts=' . $timestamp;
    $url .= '&mykey=' . $giveaway_key;
    $nothing = file_get_contents($url);
}
