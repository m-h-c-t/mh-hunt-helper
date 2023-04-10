<?php
# When you create a location detail processor add it to this list
# File naming convention: preg_replace("/[^a-z0-9]/", "_", strtolower($_POST['location']['name']));
$location_id_to_filter_name = [
    43 => 'claw_shot_city'
];

$loc_id = filter_var($_POST['location']['id'], FILTER_VALIDATE_INT);
if (is_numeric($loc_id) && array_key_exists($loc_id, $location_id_to_filter_name)) {
    include_once "locations/" . $location_id_to_filter_name[$loc_id] . ".php";
}
