<html lang="en">
<head>
<meta charset="utf-8">
    <base href="/">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body style="background-color: #fff;" class="text-center">
    <div class="container">
        <h1 class="text-center">Attraction Rate</h1><br/><br/>
<?php

require "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$query = $pdo->prepare('SELECT id, name FROM mice');
if (!$query->execute()) {
    echo 'Select all mice failed';
    return;
}

print '<form method="post"><div class="row">';
print '<select name="mouse_id" onchange="this.form.submit()" class="center-block input-lg"><option value="">Pick a mouse</option>';
while($row = $query->fetch(PDO::FETCH_ASSOC)) {
    print '<option value="' . $row['id'] . '"';
    if (!empty($_POST['mouse_id']) && ($row['id'] == $_POST['mouse_id']))
        print ' selected';

    print '>' . $row['name'] . '</option>';
}
print '</select></div></form><br/>';

// Mouse rates by location
if (!empty($_POST['mouse_id'])) {
    $statement = 'SELECT l.name, COUNT(*) as total_hunts, COUNT(m.id) as attracted_hunts
                  FROM hunts h
                  INNER JOIN locations l ON h.location_id = l.id
                  LEFT JOIN mice m ON h.mouse_id = ? AND h.mouse_id = m.id
                  GROUP BY l.name';
    $query = $pdo->prepare($statement);
    if (!$query->execute(array($_POST['mouse_id']))) {
        echo "Select attraction by mouse failed";
        return;
    }

    print '<table class="table"><tr><th>Location</th><th>Attracted</th><th>Total hunts</th><th>Rate</th></tr>';
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
        if (empty($row['attracted_hunts']))
            continue;

        print '<tr><td>' . $row['name'] .
              '</td><td>' . $row['attracted_hunts'] .
              '</td><td>' . $row['total_hunts'] .
              '</td><td>' . round($row['attracted_hunts']/$row['total_hunts'], 4)*100 . '%</td></tr>';
    }
    print '</table>';
}

$query = $pdo->prepare('SELECT count(*) as hunts, count(DISTINCT user_id) as users FROM hunts');
if (!$query->execute()) {
    echo 'Select all hunts and users failed';
    return;
}
$row = $query->fetch(PDO::FETCH_ASSOC);

print '<br/><br/><p class="text-center">' . $row['hunts'] .' total hunts contributed by ' . $row['users'] . ' hunters.<br/>If you want to help, please install <a href="https://chrome.google.com/webstore/detail/mh-hunt-helper/ghfmjkamilolkalibpmokjigalmncfek">this Chrome extension</a>.</p>';
?>
</div>
</body>
</html>