<?php
require_once "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$pdo2 = new PDO("mysql:host=$mms_servername;dbname=$mms_dbname;charset=utf8", $mms_username, $mms_password);
$pdo2->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$query = $pdo->prepare('SELECT
    COUNT(*) as hunts,
    COUNT(DISTINCT user_id) as users,
    COUNT(DISTINCT mouse_id) as mice,
    COUNT(DISTINCT location_id) as locations,
    COUNT(DISTINCT trap_id) as traps,
    COUNT(DISTINCT cheese_id) as cheese,
    COUNT(DISTINCT base_id) as bases,
    COUNT(DISTINCT charm_id) as charms
    FROM hunts');
$query->execute();
$row = $query->fetch(PDO::FETCH_ASSOC);

$query = $pdo->prepare('SELECT COUNT(*) FROM loot');
$query->execute();
$loot = $query->fetchColumn();

$query = $pdo->prepare('SELECT COUNT(*) FROM stages');
$query->execute();
$stages = $query->fetchColumn();

$query = $pdo2->prepare('SELECT COUNT(*) FROM maps');
$query->execute();
$map_types = $query->fetchColumn();

$query = $pdo2->prepare('SELECT COUNT(*) FROM map_records');
$query->execute();
$submitted_maps = $query->fetchColumn();

?>
<table class="table table-hover table-bordered" style="width:auto;margin:auto;">
    <thead>
        <tr><th colspan="2" class="text-center">Jack's Tools so far</th></tr></thead>
    <tbody>
        <tr><td>Contributors:</td><td>      <?php echo $row['users']; ?> - Thank you! :)</td></tr>
        <tr><td>Hunt submissions</td><td>   <?php echo $row['hunts']; ?></td></tr>
        <tr><td>Map submissions</td><td>    <?php echo $submitted_maps; ?></td></tr>
        <tr><td>Traps</td><td>              <?php echo $row['traps']; ?></td></tr>
        <tr><td>Bases</td><td>              <?php echo $row['bases']; ?></td></tr>
        <tr><td>Charms</td><td>             <?php echo $row['charms']; ?></td></tr>
        <tr><td>Cheese</td><td>             <?php echo $row['cheese']; ?></td></tr>
        <tr><td>Mice</td><td>               <?php echo $row['mice']; ?></td></tr>
        <tr><td>Locations</td><td>          <?php echo $row['locations']; ?></td></tr>
        <tr><td>Stages</td><td>             <?php echo $stages; ?></td></tr>
        <tr><td>Loot</td><td>               <?php echo $loot; ?></td></tr>
        <tr><td>Maps</td><td>               <?php echo $map_types; ?></td></tr>
    </tbody>
</table><br/>
<?php
    $query = $pdo->prepare('SELECT user_id, count(id) as total_hunts FROM hunts GROUP BY user_id ORDER BY total_hunts DESC LIMIT 10');
    $query->execute();
?>
<table class="table table-hover table-bordered" style="width:auto;margin:auto;">
    <thead>
        <tr><th colspan="2" class="text-center">Top Hunt Contributors</th></tr></thead>
        <tr><th class="text-center">Hunter ID</th><th>Hunts</th></tr></thead>
    <tbody>
<?php
    while ( $row = $query->fetch(PDO::FETCH_ASSOC)) {
        print "<tr><td>$row[user_id]</td><td>$row[total_hunts]</td></tr>";
    }
?>
</tbody></table>
