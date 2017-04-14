<html lang="en">
<head>
<meta charset="utf-8">
    <base href="/">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <style>
        .loader {
          border: 16px solid #f3f3f3;
          border-radius: 50%;
          border-top: 16px solid gray;
          border-bottom: 16px solid gray;
          width: 100px;
          height: 100px;
          -webkit-animation: spin 2s linear infinite;
          animation: spin 2s linear infinite;
          display: none;
          z-index: 9999;
          position: fixed;
          top: 200px;
          left: 50%;
          margin-left: -50px;
          
        }

        @-webkit-keyframes spin {
          0% { -webkit-transform: rotate(0deg); }
          100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
    </style>
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
print '<select name="mouse_id" onchange="this.form.submit();document.getElementById(\'loader\').style.display=\'block\';" class="center-block input-lg"><option value="">Pick a mouse</option>';
while($row = $query->fetch(PDO::FETCH_ASSOC)) {
    print '<option value="' . $row['id'] . '"';
    if (!empty($_POST['mouse_id']) && ($row['id'] == $_POST['mouse_id']))
        print ' selected';

    print '>' . $row['name'] . '</option>';
}
print '</select></div></form><br/>';

// Mouse rates by location
if (!empty($_POST['mouse_id'])) {
    $statement = 'SELECT l.name as location, s.name as stage, COUNT(*) as total_hunts, COUNT(m.id) as attracted_hunts
                  FROM hunts h
                  INNER JOIN locations l ON h.location_id = l.id
                  LEFT JOIN mice m ON h.mouse_id = ? AND h.mouse_id = m.id
                  LEFT JOIN stages s ON h.stage_id = s.id
                  GROUP BY h.location_id, h.stage_id
                  HAVING attracted_hunts > 0';
    $query = $pdo->prepare($statement);
    if (!$query->execute(array($_POST['mouse_id']))) {
        echo "Select attraction by mouse failed";
        return;
    }

    print '<table class="table"><tr><th>Location</th><th>Stage</th><th>Attracted</th><th>Total hunts</th><th>Rate</th></tr>';
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {

        print '<tr><td>' . $row['location'] .
              '</td><td>' . $row['stage'] .
              '</td><td>' . $row['attracted_hunts'] .
              '</td><td>' . $row['total_hunts'] .
              '</td><td>' . round($row['attracted_hunts']/$row['total_hunts'], 4)*100 . '%</td></tr>';
    }
    print '</table>';
}

$query = $pdo->prepare('SELECT COUNT(*) as hunts, COUNT(DISTINCT user_id) as users, COUNT(DISTINCT mouse_id) as mice, COUNT(DISTINCT location_id) as locations FROM hunts');
if (!$query->execute()) {
    echo 'Select all hunts and users failed';
    return;
}
$row = $query->fetch(PDO::FETCH_ASSOC);

print '<br/><br/><p class="text-center">This is in very early stages of development. Much more to come. <a href="updates.txt">Check out latest updates here</a>.<br/> If you want to help, please install <a href="https://chrome.google.com/webstore/detail/mh-hunt-helper/ghfmjkamilolkalibpmokjigalmncfek">this Chrome extension</a>.<br/>Install it on Opera using <a href="https://addons.opera.com/en/extensions/details/download-chrome-extension-9/">this</a> and on Firefox using <a href="https://addons.mozilla.org/en-US/firefox/addon/chrome-store-foxified/">this</a>.<br/>
    <br/>Stats so far<br/>Contributors:' . $row['users'] . '<br/>Hunts:' . $row['hunts'] .'<br/>Mice:' . $row['mice'] .'<br/>Locations:' . $row['locations'] .'<br/>Current version: 1.4</p>';
?>
</div>
<div id="loader" class="loader"></div>
</body>
</html>