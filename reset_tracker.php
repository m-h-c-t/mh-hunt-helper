<?php

$file_name = 'src/tracker.json';

$data = json_decode(file_get_contents($file_name));

$data->rh = ["location" => "unknown", "last_seen" => 0];

file_put_contents($file_name, json_encode($data));

