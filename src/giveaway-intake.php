<?php

require "check-direct-access.php";

// Giveaway
if ($_POST['entry_timestamp'] >= $giveaway_start_time && $_POST['entry_timestamp'] <= $giveaway_end_time) {
    recordGiveawayHunt($_POST['user_id'], $_POST['entry_timestamp']);
}

function recordGiveawayHunt($user = '', $timestamp = '') {
    global $giveaway_url, $giveaway_key;
    $url = $giveaway_url;
    $url .= '?user=' . $user . '&ts=' . $timestamp;
    $url .= '&mykey=' . $giveaway_key;
    $nothing = file_get_contents($url);
}
