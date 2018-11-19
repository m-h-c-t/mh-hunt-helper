<?php
require_once "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

echo <<< END
<table class="table table-hover table-bordered" style="width:auto;margin:auto;">
    <thead>
        <tr><th colspan="2" class="text-center">Stats so far <small class="text-muted">(updated hourly)</small></th></tr></thead>
    <tbody>
END;

$query = $pdo->prepare('SELECT * FROM stats ORDER BY sort ASC');
$query->execute();
$first_row = true;
while ( $row = $query->fetch(PDO::FETCH_ASSOC)) {
    $table_row = "<tr><td>$row[display_name]</td><td>$row[value]";
    if ($first_row) {
        $table_row .= ' - Thank you! :)';
        $first_row = false;
    }
    $table_row .= '</td></tr>';
    echo $table_row;
}

echo <<< END
    </tbody>
</table><br/>
END;



echo <<< END
<table class="table table-hover table-bordered" style="width:auto;margin:auto;">
    <thead>
        <tr><th colspan="2" class="text-center">Top Hunt Contributors <small class="text-muted">(updated hourly)</small></th></tr></thead>
        <tr><th class="text-center">Hunter</th><th>Hunts</th></tr></thead>
    <tbody>
END;

$query = $pdo->prepare('SELECT hunts FROM top_contributors ORDER BY hunts DESC');
$query->execute();
while ( $row = $query->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>(private)</td><td>$row[hunts]</td></tr>";
}

echo <<< END
</tbody></table>
END;
