<html lang="en">
<head>
<meta charset="utf-8">
    <base href="/">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/attraction.css">
</head>
<body style="text-align: center;" class="text-center">
    <div id="loader" class="loader"></div>
    <script>$("#loader").css( "display", "block" );</script>
    <div class="jumbotron">
        <h1>Jack's MH User History</h1>
        <a href="https://agiletravels.com" class="clickable"><span class="glyphicon glyphicon-chevron-left"></span> Jack's MH Tools</a>
    </div>
    <div class="container-fluid">
    I might disable this page if it takes up too many resources. Timezone is set to <span id="timezone_name">...</span><br/>
<?php

if (empty($_GET['user']) || !is_numeric($_GET['user'])) {
    print "<b>PLEASE SPECIFY A VALID USER ID</b>";
    ?><script>$("#loader").css( "display", "none" );</script><?php
    return;
}

require "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$query_string = '
    SELECT timestamp, l.name as location, s.name as stage, t.name as trap, b.name as base, ch.name as charm, h.shield, h.caught, m.name as mouse, c.name as cheese, GROUP_CONCAT(CONCAT_WS(" ", hl.amount, lt.name) separator ", ") as loot
    FROM hunts h
    LEFT JOIN locations l on h.location_id = l.id
    LEFT JOIN stages s on h.stage_id = s.id
    LEFT JOIN mice m on h.mouse_id = m.id
    LEFT JOIN cheese c on h.cheese_id = c.id
    LEFT JOIN traps t on h.trap_id = t.id
    LEFT JOIN bases b on h.base_id = b.id
    LEFT JOIN charms ch on h.charm_id = ch.id
    LEFT JOIN hunt_loot hl on h.id = hl.hunt_id
    LEFT JOIN loot lt on hl.loot_id = lt.id
    WHERE user_id = ?
    GROUP BY h.id
    ORDER BY timestamp DESC';
$query = $pdo->prepare($query_string);

if (!$query->execute(array($_GET['user']))) {
    print 'Select all hunts failed';
    return;
}
$results = $query->fetchAll(PDO::FETCH_ASSOC);
if (empty($results)) {
    print "No hunts found";
    return;
}

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
    print "<td>$row[shield]</td>";
    print "<td>$row[caught]</td>";
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
                        if (data === '1') return 'YES';
                        if (data === '0') return 'NO';
                        return '';
                    }
                }
            ]
        });
        $("#loader").css( "display", "none" );
    </script>
</body>
</html>
