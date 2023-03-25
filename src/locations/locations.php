<?php
# When you create a location detail processor add it to this list
# Files get named as preg_replace("/[^a-z0-9]/", "_", strtolower($_POST['location']['name']));
$location_id_to_filter_name = [
    43 => 'claw_shot_city'
];
