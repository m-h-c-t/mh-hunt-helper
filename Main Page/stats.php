<?php
require_once "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$query = $pdo->prepare('SELECT COUNT(*) as hunts, COUNT(DISTINCT user_id) as users, COUNT(DISTINCT mouse_id) as mice, COUNT(DISTINCT location_id) as locations, COUNT(DISTINCT stage_id) as stages, COUNT(DISTINCT trap_id) as traps, COUNT(DISTINCT cheese_id) as cheese, COUNT(DISTINCT base_id) as bases, COUNT(DISTINCT charm_id) as charms FROM hunts');
$query->execute();
$row = $query->fetch(PDO::FETCH_ASSOC);

$query = $pdo->prepare('SELECT COUNT(*) as loot FROM loot');
$query->execute();
$row2 = $query->fetch(PDO::FETCH_ASSOC);

print '
    <table class="table table-hover table-bordered" style="width:auto;margin:auto;">
        <thead>
            <tr><th colspan="2" class="text-center">Stats so far</th><tr></thead>
        <tbody>
            <tr><td>Contributors:</td><td>' . $row['users']     . ' - Thank you! :)</td></tr>
            <tr><td>Hunts</td><td>'         . $row['hunts']     . '</td></tr>
            <tr><td>Traps</td><td>'         . $row['traps']     . '</td></tr>
            <tr><td>Bases</td><td>'         . $row['bases']     . '</td></tr>
            <tr><td>Charms</td><td>'        . $row['charms']    . '</td></tr>
            <tr><td>Cheese</td><td>'        . $row['cheese']    . '</td></tr>
            <tr><td>Mice</td><td>'          . $row['mice']      .'</td></tr>
            <tr><td>Locations</td><td>'     . $row['locations'] .'</td></tr>
            <tr><td>Stages</td><td>'        . $row['stages']    . '</td></tr>
            <tr><td>Loot</td><td>'          . $row2['loot']     . '</td></tr>
        </tbody>
    </table>';
?>