<?php

require "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );


// Mouse rates by location
// Autocomplete

if (!empty($_GET['term'])) {
    $term = trim(strip_tags($_GET['term']));
    $query = $pdo->prepare('SELECT id, name FROM mice WHERE name LIKE ?');
    if (!$query->execute(array("%$term%"))) {
        print 'Select all mice failed';
        return;
    }
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $mice_array[] = ["id" => (int)$row['id'], "value" => utf8_encode(stripslashes($row['name']))];
    }
    print json_encode($mice_array);
} else if (!empty($_POST['mouse_id'])) {
    $statement = 'SELECT l.name as location, s.name as stage, COUNT(*) as total_hunts, COUNT(m.id) as attracted_hunts
                  FROM hunts h
                  INNER JOIN locations l ON h.location_id = l.id
                  LEFT JOIN mice m ON h.mouse_id = ? AND h.mouse_id = m.id
                  LEFT JOIN stages s ON h.stage_id = s.id
                  GROUP BY h.location_id, h.stage_id
                  HAVING attracted_hunts > 0';
    $query = $pdo->prepare($statement);
    if (!$query->execute(array($_POST['mouse_id']))) {
        print "Select attraction by mouse failed";
        return;
    }

    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    print json_encode($results);
}
