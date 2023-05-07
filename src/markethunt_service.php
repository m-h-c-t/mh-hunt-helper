<?php
require_once "config.php";


/**
 * Get daily marketplace data from Markethunt. Designed to be as safe as possible so mhct can 
 * continue running if Markethunt service is down.
 */
function getDailyMarketValues() {

    global $query_user_agent, $markethunt_domain;

    function fetchDailyMarketValuesJson() : string {
        $stream_context = stream_context_create([
            'http' => [
                'user_agent' => $query_user_agent,
                'timeout' => 2 // 2 seconds
            ]
        ]);

        $contents = file_get_contents("https://$markethunt_domain/items", 0, $stream_context);

        if (json_decode($contents) === NULL) { // if query fails or returns invalid json, use empty array as failsafe
            $contents = '[]';
        }

        return $contents;
    }

    $seconds_per_hour = 3600;
    $cache_filename = 'markethunt_daily.cache.json';

    if (!file_exists($cache_filename) || time() - filemtime($cache_filename) > 2 * $seconds_per_hour) {
        file_put_contents($cache_filename, fetchDailyMarketValuesJson());
    }

    $data = file_get_contents($cache_filename);

    if (!empty($data)) {
        return json_decode($data, true) ?? [];
    }

    return [];
}
