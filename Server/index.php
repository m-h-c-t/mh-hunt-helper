<?php

if (empty($_POST['location']['name'])) {
    echo "MHHH: Missing Info (trap check or friend hunt)";
    return;
}

header('Access-Control-Allow-Origin: https://www.mousehuntgame.com');

//echo 'Current PHP version: ' . phpversion(); return;

require "config.php";

// PDO
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

// Location
if (!empty($_POST['location']['name']) && !empty($_POST['location']['id'])) {
    $query = $pdo->prepare('SELECT count(*) FROM locations WHERE id = ?');
    if (!$query->execute(array($_POST['location']['id']))) {
        echo "Select location failed";
        return;
    }

    if (!$query->fetchColumn()) {
        $query = $pdo->prepare('INSERT INTO locations (id, name) VALUES (?, ?)');
        if (!$query->execute(array($_POST['location']['id'], $_POST['location']['name']))) {
            echo "Insert location failed";
            return;
        }
    }
}

// Trap
if (!empty($_POST['trap']['name']) && !empty($_POST['trap']['id'])) {
    $query = $pdo->prepare('SELECT count(*) FROM traps WHERE id = ?');
    if (!$query->execute(array($_POST['trap']['id']))) {
        echo "Select trap failed";
        return;
    }

    if (!$query->fetchColumn()) {
        $query = $pdo->prepare('INSERT INTO traps (id, name) VALUES (?, ?)');
        if (!$query->execute(array($_POST['trap']['id'], $_POST['trap']['name']))) {
            echo "Insert trap failed";
            return;
        }
    }
}

// Base
if (!empty($_POST['base']['name']) && !empty($_POST['base']['id'])) {
    $query = $pdo->prepare('SELECT count(*) FROM bases WHERE id = ?');
    if (!$query->execute(array($_POST['base']['id']))) {
        echo "Select base failed";
        return;
    }

    if (!$query->fetchColumn()) {
        $query = $pdo->prepare('INSERT INTO bases (id, name) VALUES (?, ?)');
        if (!$query->execute(array($_POST['base']['id'], $_POST['base']['name']))) {
            echo "Insert base failed";
            return;
        }
    }
}

// Charm
if (!empty($_POST['charm']['name']) && !empty($_POST['charm']['id'])) {
    $query = $pdo->prepare('SELECT count(*) FROM charms WHERE id = ?');
    if (!$query->execute(array($_POST['charm']['id']))) {
        echo "Select charm failed";
        return;
    }

    if (!$query->fetchColumn()) {
        $query = $pdo->prepare('INSERT INTO charms (id, name) VALUES (?, ?)');
        if (!$query->execute(array($_POST['charm']['id'], $_POST['charm']['name']))) {
            echo "Insert charm failed";
            return;
        }
    }
}

// Cheese
if (!empty($_POST['cheese']['name']) && !empty($_POST['cheese']['id'])) {
    $query = $pdo->prepare('SELECT count(*) FROM cheese WHERE id = ?');
    if (!$query->execute(array($_POST['cheese']['id']))) {
        echo "Select cheese failed";
        return;
    }

    if (!$query->fetchColumn()) {
        $query = $pdo->prepare('INSERT INTO cheese (id, name) VALUES (?, ?)');
        if (!$query->execute(array($_POST['cheese']['id'], $_POST['cheese']['name']))) {
            echo "Insert cheese failed";
            return;
        }
    }
}

// Mouse
$mouse_id = 0;
if (!empty($_POST['mouse'])) {
    $query = $pdo->prepare('SELECT id FROM mice WHERE name LIKE ?');
    if (!$query->execute(array($_POST['mouse']))) {
        echo "Select mouse failed";
        return;
    }

    $mouse_id = $query->fetchColumn();

    if (!$mouse_id) {
        $query = $pdo->prepare('INSERT INTO mice (name) VALUES (?)');
        if (!$query->execute(array($_POST['mouse']))) {
            echo "Insert mouse failed";
            return;
        }
        $mouse_id = $pdo->lastInsertId();
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
        // Mouse
        if (!empty($mouse_id)) {
            $fields .= ', mouse_id';
            $values .= ', :mouse_id';
            $bindings['mouse_id'] = $mouse_id;
        }

        // Charm
        if (!empty($_POST['charm']['id'])) {
            $fields .= ', charm_id';
            $values .= ', :charm_id';
            $bindings['charm_id'] = $_POST['charm']['id'];
        }

        // Shield
        if (!empty($_POST['shield'])) {
            $fields .= ', shield';
            $values .= ', :shield';
            $bindings['shield'] = 1;
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