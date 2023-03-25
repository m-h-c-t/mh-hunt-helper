<?php
# Manage Claw Shot City Hunt Details
# - at_boss (true/false)
# - poster_type (names)
# Meant to be included from hunt-intake.php
if (array_key_exists('at_boss', $_POST['hunt_details']) && array_key_exists('poster_type', $_POST['hunt_details'])) {
    $poster_name = $_POST['hunt_details']['poster_type'];
    $poster_rare = 0;
    if (str_contains($poster_name, "Rare ")) {
        $poster_rare = 1;
        $poster_name = str_replace("Rare ", "", $poster_name);
    }
    $query = $pdo->prepare('INSERT INTO hd_claw_shot_city (hunt_id, at_boss, poster_type, is_rare_poster) values (?, ?, ?, ?)');
    $query->execute(array($hunt_id, $_POST['hunt_details']['at_boss'] ? 1 : 0, $poster_name, $poster_rare));
    $processed_details['at_boss'] = True;
    $processed_details['poster_type'] = True;
}
