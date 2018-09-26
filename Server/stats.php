<?php
require_once "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$pdo2 = new PDO("mysql:host=$mms_servername;dbname=$mms_dbname;charset=utf8", $mms_username, $mms_password);
$pdo2->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$pdo3 = new PDO("mysql:host=$convertible_servername;dbname=$convertible_dbname;charset=utf8", $convertible_username, $convertible_password);
$pdo3->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$stat_variables = [
    'hunts' => $pdo,
    'users' => $pdo,
    'mice' => $pdo,
    'locations' => $pdo,
    'traps' => $pdo,
    'cheese' => $pdo,
    'bases' => $pdo,
    'charms' => $pdo,
    'loot' => $pdo,
    'stages' => $pdo,
    'maps' => $pdo2,
    'map_records' => $pdo2,
    'convertibles' => $pdo3,
    'entries' => $pdo3
    ];

$stat_variable_values = [];
foreach ($stat_variables as $name => $dbh) {
    $query = $dbh->prepare('SELECT COUNT(*) FROM ' . $name);
    $query->execute();
    $stat_variable_values[$name] = $query->fetchColumn();
}

?>
<table class="table table-hover table-bordered" style="width:auto;margin:auto;">
    <thead>
        <tr><th colspan="2" class="text-center">Jack's Tools so far</th></tr></thead>
    <tbody>
        <tr><td>Contributors:</td><td>              <?php echo $stat_variable_values['users']; ?> - Thank you! :)</td></tr>
        <tr><td>Hunt submissions</td><td>           <?php echo $stat_variable_values['hunts']; ?></td></tr>
        <tr><td>Map submissions</td><td>            <?php echo $stat_variable_values['map_records']; ?></td></tr>
        <tr><td>Convertible submissions</td><td>    <?php echo $stat_variable_values['entries']; ?></td></tr>
        <tr><td>Traps</td><td>                      <?php echo $stat_variable_values['traps']; ?></td></tr>
        <tr><td>Bases</td><td>                      <?php echo $stat_variable_values['bases']; ?></td></tr>
        <tr><td>Charms</td><td>                     <?php echo $stat_variable_values['charms']; ?></td></tr>
        <tr><td>Cheese</td><td>                     <?php echo $stat_variable_values['cheese']; ?></td></tr>
        <tr><td>Mice</td><td>                       <?php echo $stat_variable_values['mice']; ?></td></tr>
        <tr><td>Locations</td><td>                  <?php echo $stat_variable_values['locations']; ?></td></tr>
        <tr><td>Stages</td><td>                     <?php echo $stat_variable_values['stages']; ?></td></tr>
        <tr><td>Loot</td><td>                       <?php echo $stat_variable_values['loot']; ?></td></tr>
        <tr><td>Maps</td><td>                       <?php echo $stat_variable_values['maps']; ?></td></tr>
        <tr><td>Convertibles</td><td>               <?php echo $stat_variable_values['convertibles']; ?></td></tr>
    </tbody>
</table><br/>
<?php
    $query = $pdo->prepare('SELECT count(id) as total_hunts FROM hunts GROUP BY user_id ORDER BY total_hunts DESC LIMIT 10');
    $query->execute();
?>
<table class="table table-hover table-bordered" style="width:auto;margin:auto;">
    <thead>
        <tr><th colspan="2" class="text-center">Top Hunt Contributors</th></tr></thead>
        <tr><th class="text-center">Hunter</th><th>Hunts</th></tr></thead>
    <tbody>
<?php
    while ( $row = $query->fetch(PDO::FETCH_ASSOC)) {
        print "<tr><td>(private)</td><td>$row[total_hunts]</td></tr>";
    }
?>
</tbody></table>
