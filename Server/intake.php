<?php

require "configs.php";

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
        $query->execute(array($_POST[$item['name']]['id']));

        if (!$query->fetchColumn()) {
            $query = $pdo->prepare('INSERT INTO ' . $item['table_name'] . ' (id, name) VALUES (?, ?)');
            $query->execute(array($_POST[$item['name']]['id'], $_POST[$item['name']]['name']));
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
        $query->execute(array($_POST[$item['name']]));

        ${$item['name'] . "_id"} = $query->fetchColumn();

        if (!${$item['name'] . "_id"}) {
            $query = $pdo->prepare('INSERT INTO ' . $item['table_name'] . ' (name) VALUES (?)');
            $query->execute(array($_POST[$item['name']]));
            ${$item['name'] . "_id"} = $pdo->lastInsertId();
        }
    }
}

if (empty($_POST['cheese']['name']) || empty($_POST['cheese']['id']) || !is_numeric($_POST['cheese']['id'])) {
    error_log('Cheese missing');
    thanks();
}

$query = $pdo->prepare('SELECT count(*) FROM hunts WHERE user_id = :user_id AND entry_id = :entry_id AND timestamp = :entry_timestamp');
$query->execute(array('user_id' => $_POST['user_id'], 'entry_id' => $_POST['entry_id'], 'entry_timestamp' => $_POST['entry_timestamp']));

if ($query->fetchColumn()) {
    error_log("Hunt already existed");
    thanks();
}

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
if (!empty($_POST['shield']) && $_POST['shield'] !== 'false') {
    $fields .= ', shield';
    $values .= ', :shield';
    $bindings['shield'] = 1;
}

// Extension Version
if (!empty($_POST['extension_version'])) {
    $fields .= ', extension_version';
    $values .= ', :extension_version';
    $bindings['extension_version'] = formatVersion($_POST['extension_version']);
}

$query = $pdo->prepare("INSERT INTO hunts ($fields) VALUES ($values)");
$query->execute($bindings);

$hunt_id = $pdo->lastInsertId();

if (!empty($_POST['loot']) && $hunt_id > 0) {
    $loot_array = [];
    foreach ($_POST['loot'] as $loot_item) {
        $loot_item['amount'] = str_replace(",", "", $loot_item['amount']);
        if (!is_numeric($loot_item['amount']) || $loot_item['amount'] < 1) {
            continue;
        }

        if (!empty($loot_array[$loot_item['name']])) {
            $loot_array[$loot_item['name']] += $loot_item['amount'];
        } else {
            $loot_array[$loot_item['name']] = $loot_item['amount'];
        }
    }

    foreach ($loot_array as $loot_name => $loot_amount) {

        $query = $pdo->prepare("SELECT id FROM loot WHERE name LIKE ?");
        $query->execute(array($loot_name));
        $loot_id = $query->fetchColumn();

        if (!$loot_id) {
            $query = $pdo->prepare('INSERT INTO loot (name) VALUES (?)');
            $query->execute(array($loot_name));
            $loot_id = $pdo->lastInsertId();
        }

        $query = $pdo->prepare('INSERT INTO hunt_loot (hunt_id, loot_id, amount) VALUES (?, ?, ?)');
        $query->execute(array($hunt_id, $loot_id, $loot_amount));
    }
}

thanks();

function thanks() {
    die("MHHH: Thanks for the MH hunt info!");
}

function formatVersion($version) {
	if (strpos($version, '.') !== false) {
		$version_array = explode('.', $version);
		$new_version = '';
		foreach($version_array as $piece) {
			$new_version .= str_pad($piece,  2, "0", STR_PAD_LEFT);
		}
		$version = intval($new_version);
	}
	return $version;
}

function recordRelicHunter() {
    $query = $pdo->prepare('SELECT rh_environment FROM mhhunthelper.states');
    $query->execute();
    if ($_POST['rh_environment'] == $query->fetchColumn()) {
        $query = $pdo->prepare('UPDATE mhhunthelper.states SET rh_timestamp = NOW()');
        $query->execute();
        return;
    }

    $query = $pdo->prepare('UPDATE mhhunthelper.states SET rh_environment = ?, rh_timestamp = NOW()');
    $query->execute(array($_POST['rh_environment']));
}

?>
