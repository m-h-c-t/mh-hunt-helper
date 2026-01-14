<?php

require_once "config.php";
require_once "db-connect.php";

$title = "MHCT Relic Hunter Tracker";
$js = "scripts/rh-tracker.js";
$load_datatable_libraries = true;
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

// Show current location
print "<h3>Current RH Location: $location.<br/>
<small>Last seen (after last move): $time_since ago.</small></h3><br/>";


// Aggregated locations
print "<h3>Aggregated RH Stats<br/><small>(updated hourly)</small></h3>";

$query = $pdo->prepare('SELECT count(*) as total_seen, count(distinct location_id) as unique_locations FROM rh_tracker');
$query->execute();
$totals = $query->fetch(PDO::FETCH_ASSOC);

print "Seen RH $totals[total_seen] times in $totals[unique_locations] unique locations<br/>";

echo <<< END
<div class="table-responsive"><table id="aggregated_locations" class="table table-hover table-striped table-bordered">
    <thead>
        <tr><th class="text-center">Location</th><th>Times Seen</th><th>Percentage Seen</th><th>Last Seen</th></tr></thead>
    <tbody>
END;

$query = $pdo->prepare('SELECT l.name, times_seen, 1, last_seen FROM aggr_rh_locations a JOIN locations l on a.location_id = l.id ORDER BY l.name ASC');
$query->execute();
while ( $row = $query->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>$row[name]</td><td>$row[times_seen]</td><td>" . round($row['times_seen']/$totals['total_seen']*100, 2) . "&#37;</td><td>$row[last_seen]</td></tr>";
}

print "</tbody></table></div><br/>";


// History
print "<h3>Daily RH Location History</h3>";
echo <<< END
<table class="table table-hover table-striped table-bordered">
    <thead>
        <tr><th class="text-center">Date</th><th class="text-center">Location</th></tr></thead>
    <tbody>
END;

$query = $pdo->prepare('SELECT t.date, l.name FROM rh_tracker t JOIN locations l on t.location_id = l.id ORDER BY date DESC');
$query->execute();
while ( $row = $query->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>$row[date]</td><td>$row[name]</td></tr>";
}

print "</tbody></table><br/>";

require_once "common-footer.php";
