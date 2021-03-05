<html lang="en">
<head>
    <?php require "common_head.php"; ?>
</head>
<body style="text-align: center;" class="text-center">
    <div id="loader" class="loader"></div>
    <script>$("#loader").css( "display", "block" );</script>
    <div class="jumbotron">
        <h1>MHCT User History</h1>
        <a href="/" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> MHCT Tools</a>
    </div>
    <div class="container-fluid">
<?php

if (empty($_GET['user']) || !is_numeric($_GET['user'])) {
    print "<b>PLEASE SPECIFY A VALID USER ID</b>";
    ?><script>$("#loader").css( "display", "none" );</script><?php
    return;
} else {
    $not_direct_access_id = true;
    $encrypted_user_id = $_GET['user'];
    require "id_modifier.php";
}

require "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$query = $pdo->prepare('SELECT id FROM users WHERE digest LIKE ?');
$query->execute(array($encrypted_user_id));

$user_id = $query->fetchColumn();

$count_query_string = "SELECT count(*) FROM hunts where user_id = ?";
$query2 = $pdo->prepare($count_query_string);
$query2->execute(array($user_id));
$count = $query2->fetchColumn();

if (empty($count)) {
    print "<br/>No hunts found<br/>";
    ?><script>$("#loader").css( "display", "none" );</script><?php
    return;
}
print 'Timezone is set to <span id="timezone_name">...</span>.Total hunts found: ' . $count . '.';
if ($count > 1000) {
    print " Limiting hunts to latest 1000.";
}
print "<br/>";

$query_string = '
    SELECT timestamp, l.name as location, GROUP_CONCAT(DISTINCT s.name SEPARATOR ", ") as stage, t.name as trap, b.name as base, ch.name as charm, h.shield, h.caught, m.name as mouse, c.name as cheese, GROUP_CONCAT(DISTINCT CONCAT_WS(" ", hl.amount, CONCAT(lt.name, IF(hl.lucky, "(L)", ""))) SEPARATOR ", ") as loot
    FROM (SELECT * from hunts WHERE user_id = ? ORDER BY id DESC LIMIT 1000) h
    INNER JOIN locations l on h.location_id = l.id
    LEFT JOIN hunt_stage hs on h.id = hs.hunt_id
    LEFT JOIN stages s on hs.stage_id = s.id
    LEFT JOIN mice m on h.mouse_id = m.id
    INNER JOIN cheese c on h.cheese_id = c.id
    INNER JOIN traps t on h.trap_id = t.id
    INNER JOIN bases b on h.base_id = b.id
    LEFT JOIN charms ch on h.charm_id = ch.id
    LEFT JOIN hunt_loot hl on h.id = hl.hunt_id
    LEFT JOIN loot lt on hl.loot_id = lt.hg_item_id
    GROUP BY h.id
    ORDER BY timestamp DESC';
$query = $pdo->prepare($query_string);
$query->execute(array($user_id));
$results = $query->fetchAll(PDO::FETCH_ASSOC);

print '<div class="table-responsive"><table id="results_table" class="table table-striped table-bordered table-hover display"><thead>
<th>Time</th>
<th>Location</th>
<th>Stage</th>
<th>Trap</th>
<th>Base</th>
<th>Charm</th>
<th>Cheese</th>
<th>Shield</th>
<th>Caught</th>
<th>Mouse</th>
<th>Loot</th>
</thead><tbody>';

foreach ($results as $row) {
    print "<tr>";
    print "<td>$row[timestamp]</td>";
    print "<td>$row[location]</td>";
    print "<td>$row[stage]</td>";
    print "<td>$row[trap]</td>";
    print "<td>$row[base]</td>";
    print "<td>$row[charm]</td>";
    print "<td>$row[cheese]</td>";
    print "<td class='text-center'>$row[shield]</td>";
    print "<td class='text-center'>$row[caught]</td>";
    print "<td>$row[mouse]</td>";
    print "<td>$row[loot]</td>";
    print "</tr>";
}
print "</tbody></table></div>";

?>
    </div>
    <script type="text/javascript">
        $('#timezone_name').html(Intl.DateTimeFormat().resolvedOptions().timeZone);
        var temp_date = new Date;
        $('#results_table').DataTable( {
            dom: 'lBftpri',
            order: [[0, 'desc']],
            buttons: [
            {
                extend: 'excelHtml5',
                title: 'MH Data export'
            },
            {
                extend: 'csvHtml5',
                title: 'MH Data export'
            }
            ],
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    render: function ( data, type, row ) {
                        temp_date.setTime(data * 1000);
                        var formatted_date = temp_date.getFullYear() + "-"
                            + ((temp_date.getMonth() + 1) < 10 ? "0" : "" ) + (temp_date.getMonth() + 1) + "-"
                            + (temp_date.getDate() < 10 ? "0" : "" ) + temp_date.getDate();
                        var formatted_time = (temp_date.getHours() < 10 ? "0" : "" ) + temp_date.getHours() + ":"
                            + (temp_date.getMinutes() < 10 ? "0" : "" ) + temp_date.getMinutes();
                        return '<span style="white-space: nowrap;">' + formatted_date + '</span> <span style="white-space: nowrap;">' + formatted_time + '</span>';
                    }
                },
                {
                    "targets": [ 7, 8 ],
                    render: function ( data, type, row ) {
                        if (data === '1') return '<span class="text-success glyphicon glyphicon-ok"><span style="opacity:0">1</span></span>';
                        if (data === '0') return '<span class="text-danger glyphicon glyphicon-remove"><span style="opacity:0">0</span></span>';
                        return '';
                    }
                }
            ]
        });
        $("#loader").css( "display", "none" );
    </script>
<?php require_once "new-common-footer.php"; ?>
</body>
</html>
