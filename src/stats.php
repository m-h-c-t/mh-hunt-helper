<?php
require_once "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8", $username, $password);
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
<table class="table table-hover table-bordered text-center" style="width:auto;margin:auto;">
    <thead>
        <tr><th colspan="2" class="text-center">Top Hunt Contributors <small class="text-muted">(updated hourly)</small></th></tr></thead>
        <tr><th class="text-center">Hunter</th><th class="text-center">Hunts</th></tr></thead>
    <tbody>
END;

$query = $pdo->prepare('SELECT hunts FROM top_contributors ORDER BY hunts DESC');
$query->execute();
while ( $row = $query->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td>(private)</td><td>$row[hunts]</td></tr>";
}

echo <<< END
</tbody></table><br/>
END;

echo <<< END
<table class="table table-hover table-bordered" style="width:auto;margin:auto;">
    <thead>
        <tr><th colspan="2" class="text-center">Mice missing / not tracked <small class="text-muted">(updated manually)</small></th></tr></thead>
        <tr><th class="text-center">&num;</th><th>Mouse</th></tr></thead>
    <tbody>
END;

$query = $pdo->prepare('SELECT name FROM missing_mice ORDER BY name ASC');
$query->execute();
$index = 1;
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr><td class='text-center'>$index</td><td>$row[name]</td></tr>";
    $index++;
}

echo <<< END
    <tr><td></td><td>And all the new mice until caught.</td></tr>
</tbody></table>
END;
