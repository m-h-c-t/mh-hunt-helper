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
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.4/jstz.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
</head>
<body style="text-align: center;" class="text-center">
 <!-- Jumbotron -->
    <div class="jumbotron" style='background: url("/images/croppedcat.jpg") center no-repeat; text-shadow: 0 0 1em black; color: white;'>
        <div class="container-fluid">
            <h1>MouseHunt User History</h1>
        <!-- <p>Tool to help you catch map mice asap.</p> -->
        </div>
    </div>
    <div class="container-fluid">
    I might disable this if it takes up too many resources.
<?php

if (empty($_GET['user'])) {
    print "please specify user id";
    return;
}
?>
<script type="text/javascript">
$( function() {
    var tz = jstz.determine(); // Determines the time zone of the browser client
    document.cookie = "tz=" +  tz.name();
});
</script>
<?php  

$timezone = empty($_COOKIE['tz']) ? 'UTC' : $_COOKIE['tz'];
print "Timezone is set to " . $timezone . ".<br/>";

require "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$query_parameters = array();
$query_string = '
    SELECT timestamp, l.name as location, s.name as stage, t.name as trap, b.name as base, ch.name as charm, h.shield, h.attracted, h.caught, m.name as mouse
    FROM hunts h
    LEFT JOIN locations l on h.location_id = l.id
    LEFT JOIN stages s on h.stage_id = s.id
    LEFT JOIN mice m on h.mouse_id = m.id
    LEFT JOIN cheese c on h.cheese_id = c.id
    LEFT JOIN traps t on h.trap_id = t.id
    LEFT JOIN bases b on h.base_id = b.id
    LEFT JOIN charms ch on h.charm_id = ch.id';
    
if (is_numeric($_GET['user'])) {
    $query_string .= ' WHERE user_id = ?';
    $query_parameters[] = $_GET['user'];
} else if ($_GET['user'] !== 'all') {
    return;
}
    
$query_string .= ' ORDER BY timestamp DESC';
$query = $pdo->prepare($query_string);

if (!$query->execute($query_parameters)) {
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
<th>Shield</th>
<th>Attracted</th>
<th>Caught</th>
<th>Mouse</th>
</thead><tbody>';

$time = new DateTime();
$time->setTimeZone(new DateTimeZone($timezone));
foreach ($results as $row) {
    $time->setTimestamp($row["timestamp"]);
    print "<tr>";
    print "<td>" . $time->format('m-d-y H:i') . "</td>";
    print "<td>$row[location]</td>";
    print "<td>$row[stage]</td>";
    print "<td>$row[trap]</td>";
    print "<td>$row[base]</td>";
    print "<td>$row[charm]</td>";
    print "<td>" . ($row['shield'] ? "YES" : "NO" ) . "</td>";
    print "<td>" . ($row['attracted'] ? "YES" : "NO" ) . "</td>";
    print "<td>" . ($row['caught'] ? "YES" : "NO" ) . "</td>";
    print "<td>$row[mouse]</td>";
    print "</tr>";
}
print "</tbody></table></div>";

?>
    </div>
    <script type="text/javascript">
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
            ]
        });
    </script>
</body>
</html>
