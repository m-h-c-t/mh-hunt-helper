<?php

$http_origin = $_SERVER['HTTP_ORIGIN'];

if ($http_origin == "https://www.mousehuntgame.com" || $http_origin == "http://www.mousehuntgame.com") {
    header("Access-Control-Allow-Origin: $http_origin");
}

if (empty($_POST['location']['name'])) {
    echo "MHHH: Missing Info (will try better next hunt!)";
    return;
}

//echo 'Current PHP version: ' . phpversion(); return;

require "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

// id and value intake
$id_value_intake = array(
    ["name" => "location", "table_name" => "locations", "optional" => false],
    ["name" => "trap",     "table_name" => "traps",     "optional" => false],
    ["name" => "base",     "table_name" => "bases",     "optional" => false],
    ["name" => "charm",    "table_name" => "charms",    "optional" => true],
    ["name" => "cheese",   "table_name" => "cheese",    "optional" => false]
);
foreach($id_value_intake as $item) {
    if (!empty($_POST[$item['name']]['name']) && !empty($_POST[$item['name']]['id'])) {
        $query = $pdo->prepare('SELECT count(*) FROM ' . $item['table_name'] . ' WHERE id = ?');
        if (!$query->execute(array($_POST[$item['name']]['id']))) {
            echo "Select " . $item['name'] . " failed";
            return;
        }

        if (!$query->fetchColumn()) {
            $query = $pdo->prepare('INSERT INTO ' . $item['table_name'] . ' (id, name) VALUES (?, ?)');
            if (!$query->execute(array($_POST[$item['name']]['id'], $_POST[$item['name']]['name']))) {
                echo "Insert " . $item['name'] . " failed";
                return;
            }
        }
    }
}

// only value intake
$value_intake = array(
    ["name" => "mouse", "table_name" => "mice",   "optional" => true],
    ["name" => "stage", "table_name" => "stages", "optional" => true]
);

foreach($value_intake as $item) {
    ${$item['name'] . "_id"} = 0;
    if (!empty($_POST[$item['name']])) {
        $query = $pdo->prepare('SELECT id FROM ' . $item['table_name'] . ' WHERE name LIKE ?');
        if (!$query->execute(array($_POST[$item['name']]))) {
            echo "Select " . $item['name'] . " failed";
            return;
        }

        ${$item['name'] . "_id"} = $query->fetchColumn();

        if (!${$item['name'] . "_id"}) {
            $query = $pdo->prepare('INSERT INTO ' . $item['table_name'] . ' (name) VALUES (?)');
            if (!$query->execute(array($_POST[$item['name']]))) {
                echo "Insert " . $item['name'] . " failed";
                return;
            }
            ${$item['name'] . "_id"} = $pdo->lastInsertId();
        }
    }
}

if (!empty($_POST['entry_id']) &&
    !empty($_POST['entry_timestamp']) &&
    !empty($_POST['user_id']) &&
    !empty($_POST['location']['id']) &&
    !empty($_POST['trap']['id']) &&
    !empty($_POST['base']['id']) &&
    !empty($_POST['cheese']['id']) &&
    array_key_exists('attracted', $_POST) &&
    array_key_exists('caught', $_POST)
    ) {

    $query = $pdo->prepare('SELECT count(*) FROM hunts WHERE user_id = :user_id AND entry_id = :entry_id AND timestamp = :entry_timestamp');
    if (!$query->execute(array('user_id' => $_POST['user_id'], 'entry_id' => $_POST['entry_id'], 'entry_timestamp' => $_POST['entry_timestamp']))) {
        echo "Select hunt failed";
        return;
    }

    if (!$query->fetchColumn()) {
        $fields = 'user_id, entry_id, timestamp, location_id, trap_id, base_id, cheese_id, caught, attracted';
        $values = ':user_id, :entry_id, :entry_timestamp, :location_id, :trap_id, :base_id, :cheese_id, :caught, :attracted';
        $bindings = array(
            'user_id' => $_POST['user_id'],
            'entry_id' => $_POST['entry_id'],
            'entry_timestamp' => $_POST['entry_timestamp'],
            'location_id' => $_POST['location']['id'],
            'trap_id' => $_POST['trap']['id'],
            'base_id' => $_POST['base']['id'],
            'cheese_id' => $_POST['cheese']['id'],
            'caught' => $_POST['caught'],
            'attracted' => $_POST['attracted']
            );


        // Optionals

        foreach ($id_value_intake as $item) {
            if (!$item['optional'])
                continue;

            if (!empty($_POST[$item['name']]['id'])) {
                $fields .= ', ' . $item['name'] . "_id";
                $values .= ', :' . $item['name'] . "_id";
                $bindings[$item['name'] . "_id"] = $_POST[$item['name']]['id'];
            }
        }

        foreach ($value_intake as $item) {
            if (!$item['optional'])
                continue;

            if (!empty(${$item['name'] . "_id"})) {
                $fields .= ', ' . $item['name'] . "_id";
                $values .= ', :' . $item['name'] . "_id";
                $bindings[$item['name'] . "_id"] = ${$item['name'] . "_id"};
            }
        }

        // Shield
        if (!empty($_POST['shield'])) {
            $fields .= ', shield';
            $values .= ', :shield';
            $bindings['shield'] = 1;
        }

        // Extension Version
        if (!empty($_POST['extension_version'])) {
            $fields .= ', extension_version';
            $values .= ', :extension_version';
            $bindings['extension_version'] = $_POST['extension_version'];
        }

        $query = $pdo->prepare("INSERT INTO hunts ($fields) VALUES ($values)");
        if (!$query->execute($bindings)) {
            echo "Insert hunt failed";
            return;
        }
    }
}

echo "MHHH: Thanks for the hunt info!";
return;
?>
