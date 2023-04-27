<?php

$title = "MHCT Latest Rejections List";

// $fluid_container = true;
// $load_datatable_libraries = true;

require_once "common-header.php";
require_once "config.php";
require_once "db-connect.php";

$query = $pdo->prepare("SELECT MAX(extension_version) FROM rejections");
$query->execute(array());
$max_ext_version = $query->fetchColumn();

$query_string = '
    SELECT r.*
    FROM rejections r WHERE r.extension_version = (?)
    ORDER BY count DESC';
$query = $pdo->prepare($query_string);
$query->execute(array($max_ext_version));
$results = $query->fetchAll(PDO::FETCH_ASSOC);


print "Rejections for transitions (sorted by highest count first) for latest extension version: $max_ext_version.<br>";
print 'This list will regenerate slowly over time for each extension release.<br>';
print 'This is for internal use only, and is almost never a complete comprehensive list.<br>';

print '<div class="table-responsive"><table id="results_table" class="table table-striped table-bordered table-hover display"><thead>
<th>Pre Location</th>
<th>Post Location</th>
<th>Count</th>
</thead><tbody>';

foreach ($results as $row) {
    print "<tr>";
    print "<td>$row[prelocation]</td>";
    print "<td>$row[postlocation]</td>";
    print "<td>$row[count]</td>";
    print "</tr>";
}
print "</tbody></table></div>";
