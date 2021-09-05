<?php

require_once "config.php";
require_once "db-connect.php";

$title = "MHCT Relic Hunter Tracker";
require_once "common-header.php";
$file_name = 'tracker.json';
$location = 'Unknown';
$time_since = 'a while';
$data = file_get_contents($file_name);

if (!empty($data)) {
    $data = json_decode($data);

    if (!empty($data->rh->location)) {
        $location = $data->rh->location;
    }

    if ($data->rh->last_seen != 0) {
        $last_seen = $data->rh->last_seen;
        $date = new DateTime("@$last_seen");
        $now = new DateTime();

        $time_since = $date->diff($now)->format("%h hour(s) and %i minute(s)");
    }
}


print "<h3>Current Relic Hunter Location: $location.<br/>
<small>Last seen (after last move): $time_since ago.</small></h3><br/><br/>";


echo <<< END
<table class="table table-hover table-bordered" style="width:auto;margin:auto;">
    <thead>
        <tr><th colspan="2" class="text-center">RH History</th></tr></thead>
    <tbody>
END;

$query = $pdo->prepare('SELECT t.date, l.name FROM rh_tracker t JOIN locations l on t.location_id = l.id ORDER BY date DESC');
$query->execute();
while ( $row = $query->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>$row[date]</td><td>$row[name]</td></tr>";
}

echo <<< END
    </tbody>
</table><br/>
END;

require_once "common-footer.php";
