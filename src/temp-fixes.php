<?php

require_once "check-direct-access.php";

if (!empty($_POST['mouse']) && $_POST['mouse'] == 'Frostwing Commander' && $_POST['location']['id'] == 39)
{
	sendResponse('success', "Thanks for the hunt info!");
}

if (!empty($_POST['mouse']) && strpos($_POST['mouse'], 'Lucky me, a prize mouse wandered by') !== false) {
	sendResponse('success', "Thanks for the hunt info!");
}

#check if still firing
if (!empty($_POST['stage']) && $_POST['location']['id'] == 51) {
    $zokor_stages = [
        "Garden" =>     "Farming 0+",
        "Study" =>      "Scholar 15+",
        "Shrine" =>     "Fealty 15+",
        "Outskirts" =>  "Tech 15+",
        "Room" =>       "Treasure 15+",
        "Minotaur" =>   "Lair - Each 30+",
        "Temple" =>     "Fealty 50+",
        "Auditorium" => "Scholar 50+",
        "Farmhouse" =>  "Farming 50+",
        "Center" =>     "Tech 50+",
        "Vault" =>      "Treasure 50+",
        "Library" =>    "Scholar 80+",
        "Manaforge" =>  "Tech 80+",
        "Sanctum" =>    "Fealty 80+"
        ];

    foreach ($zokor_stages as $zname => $zstage) {
        if (strpos($_POST['stage'], $zname) !== false) {
            error_log('zokor stages still fire');
            $_POST['stage'] = $zstage;
        }
    }
}
