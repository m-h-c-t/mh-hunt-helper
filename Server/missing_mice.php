<?php

require_once "config.php";

global $pdo, $servername, $dbname, $username, $password;
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );


// Mice not present in mhhunthelper but present in mhmaphelper
$query = $pdo->prepare('
    SELECT m2.name as mouse
    FROM mhhunthelper.mice m1
    RIGHT JOIN mhmaphelper.mice m2 ON m1.id = m2.mhhh_id
    WHERE m1.id IS NULL
    ORDER BY m2.name');
$query->execute();
?>
<h4>The following mice are missing (from most tools based on crowd sourced data) due to several factors:</h4>
1) Majority is due to events have not happened since I started this tool.<br/>
2) Mouse is a transition mouse between places and it's impossible to accurately determine where it was caught with current MH code. <br/>(This will hopefully be solved when MH Devs add API in MH 4.0)

<table class="table table-hover table-bordered" style="width:auto;margin:auto;">
    <thead>
        <tr><th colspan="2" class="text-center">Mice missing from tools (except map helper)</th></tr></thead>
    <tbody>
<?php
	$mouse_count = 1;
    while ( $row = $query->fetch(PDO::FETCH_ASSOC)) {
        print "<tr><td>$mouse_count</td><td>$row[mouse]</td></tr>";
		$mouse_count++;
    }
?>
</tbody></table>
