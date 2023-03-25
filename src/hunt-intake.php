<?php

require_once "check-direct-access.php";
require_once "check-userid.php";
require_once "send_response.php";

// Keep track of processed details
$processed_details = array();

// Field check

if (empty($_POST['location']['name'])) {
    error_log("Location name is missing");
    error_log('USER: ' . $_POST['hunter_id_hash']);
    sendResponse('success', "Thanks for the hunt info!");
}

if (empty($_POST['trap']['name'])) {
    error_log("Trap name is missing");
    error_log('USER: ' . $_POST['hunter_id_hash']);
    sendResponse('success', "Thanks for the hunt info!");
}

if (empty($_POST['base']['name'])) {
    error_log("Base name is missing");
    error_log('USER: ' . $_POST['hunter_id_hash']);
    sendResponse('success', "Thanks for the hunt info!");
}

if (empty($_POST['entry_id']) || !is_numeric($_POST['entry_id'])) {
    error_log("Entry id is missing");
    error_log('USER: ' . $_POST['hunter_id_hash']);
    sendResponse('success', "Thanks for the hunt info!");
}

if (empty($_POST['location']['id']) || !is_numeric($_POST['location']['id'])) {
    error_log("Location ID is missing");
    error_log('USER: ' . $_POST['hunter_id_hash']);
    sendResponse('success', "Thanks for the hunt info!");
}

if (empty($_POST['trap']['id']) || !is_numeric($_POST['trap']['id'])) {
    error_log("Trap ID is missing");
    error_log('USER: ' . $_POST['hunter_id_hash']);
    sendResponse('success', "Thanks for the hunt info!");
}

if (empty($_POST['base']['id']) || !is_numeric($_POST['base']['id'])) {
    error_log("Base ID is missing");
    error_log('USER: ' . $_POST['hunter_id_hash']);
    sendResponse('success', "Thanks for the hunt info!");
}

if (!array_key_exists('attraction_bonus', $_POST) || !is_numeric($_POST['attraction_bonus'])) {
    error_log("Attraction bonus is missing");
    error_log('USER: ' . $_POST['hunter_id_hash']);
    sendResponse('success', "Thanks for the hunt info!");
}

if (!array_key_exists('total_luck', $_POST) || !is_numeric($_POST['total_luck'])) {
    error_log("Total Luck is missing");
    error_log('USER: ' . $_POST['hunter_id_hash']);
    sendResponse('success', "Thanks for the hunt info!");
}

if (!array_key_exists('total_power', $_POST) || !is_numeric($_POST['total_power'])) {
    error_log("Total Power is missing");
    error_log('USER: ' . $_POST['hunter_id_hash']);
    sendResponse('success', "Thanks for the hunt info!");
}

if (!array_key_exists('attracted', $_POST)) {
    error_log("Attracted field is missing");
    error_log('USER: ' . $_POST['hunter_id_hash']);
    sendResponse('success', "Thanks for the hunt info!");
}

if (!array_key_exists('caught', $_POST)) {
    error_log("Location name is missing");
    error_log('USER: ' . $_POST['hunter_id_hash']);
    sendResponse('success', "Thanks for the hunt info!");
}

$is_lucky_catch = 0;
if (array_key_exists('hunt_details', $_POST) && array_key_exists('is_lucky_catch', $_POST['hunt_details'])) {
    $is_lucky_catch = strtolower($_POST['hunt_details']['is_lucky_catch']) == "true" ? 1 : 0;
    $processed_details['is_lucky_catch'] = True;
}

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
    ["name" => "mouse", "table_name" => "mice", "optional" => true]
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
    die();
}

$query = $pdo->prepare('SELECT count(*) FROM hunts WHERE user_id = :user_id AND entry_id = :entry_id AND timestamp = :entry_timestamp');
$query->execute(array('user_id' => $user_id, 'entry_id' => $_POST['entry_id'], 'entry_timestamp' => $_POST['entry_timestamp']));

if ($query->fetchColumn()) {
    error_log("Hunt already existed");
    die();
}

$fields = [
    'entry_id',
    'timestamp',
    'location_id',
    'trap_id',
    'base_id',
    'cheese_id',
    'caught',
    'attracted',
    'user_id',
    'attraction_bonus',
    'total_power',
    'total_luck',
    'extension_version',
    'shield',
    'is_lucky_catch'
];
$fields = implode(',', $fields);

$values = [
    ':entry_id',
    ':entry_timestamp',
    ':location_id',
    ':trap_id',
    ':base_id',
    ':cheese_id',
    ':caught',
    ':attracted',
    ':user_id',
    ':attraction_bonus',
    ':total_power',
    ':total_luck',
    ':extension_version',
    ':shield',
    ':is_lucky_catch'
];
$values = implode(',', $values);

$bindings = array(
    'entry_id'          => $_POST['entry_id'],
    'entry_timestamp'   => $_POST['entry_timestamp'],
    'location_id'       => $_POST['location']['id'],
    'trap_id'           => $_POST['trap']['id'],
    'base_id'           => $_POST['base']['id'],
    'cheese_id'         => $_POST['cheese']['id'],
    'caught'            => $_POST['caught'],
    'attracted'         => $_POST['attracted'],
    'user_id'           => $user_id,
    'attraction_bonus'  => $_POST['attraction_bonus'],
    'total_power'       => $_POST['total_power'],
    'total_luck'        => $_POST['total_luck'],
    'extension_version' => $_POST['extension_version'],
    'shield'            => ((!empty($_POST['shield']) && $_POST['shield'] !== 'false') ? 1 : 0),
    'is_lucky_catch'    => $is_lucky_catch
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

try {
    $pdo->beginTransaction();
    $query = $pdo->prepare("INSERT INTO hunts ($fields) VALUES ($values)");
    $query->execute($bindings);

    $hunt_id = $pdo->lastInsertId();

    // Stage(s)
    if (!empty($_POST['stage']) && !empty($hunt_id)) {
        if (is_string($_POST['stage'])) {
            $_POST['stage'] = array($_POST['stage']);
        }

        foreach ($_POST['stage'] as $stage_name) {
            $stage_id = 0;
            $query = $pdo->prepare('SELECT id FROM mhhunthelper.stages WHERE name LIKE ?');
            $query->execute(array($stage_name));

            $stage_id = $query->fetchColumn();

            if (!$stage_id) {
                $query = $pdo->prepare('INSERT INTO mhhunthelper.stages (name) VALUES (?)');
                $query->execute(array($stage_name));
                $stage_id = $pdo->lastInsertId();
            }


            if (!empty($stage_id)) {
                $query = $pdo->prepare("INSERT INTO hunt_stage (hunt_id, stage_id) VALUES (?, ?)");
                $query->execute(array($hunt_id, $stage_id));
            }
        }
    }


    // Loot
    if (!empty($_POST['loot']) && $hunt_id > 0) {
        $loot_array = [];
        foreach ($_POST['loot'] as $loot_item) {

            // Amount checks
            if (!is_numeric($loot_item['amount']) || $loot_item['amount'] < 1) {
                continue;
            }

            // Lucky checks
            $lucky = null;
            if (!empty($loot_item['lucky'])) { $lucky = $loot_item['lucky'] === 'true' ? 1 : 0; }

            // Hitgrab item id checks
            $loot_item_id = null;
            if (is_numeric($loot_item['id'])) { $loot_item_id = $loot_item['id']; }

            // Plural name checks
            $plural_name = null;
            if (!empty($loot_item['plural_name'])) { $plural_name = $loot_item['plural_name']; }

            // Single name checks
            $single_name = null;
            if (!empty($loot_item['name'])) { $single_name = $loot_item['name']; }

            // Insert/Update loot table
            $query = $pdo->prepare("INSERT INTO loot (name, plural_name, id)
            VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE
            name = VALUES(name), plural_name = COALESCE(VALUES(plural_name), plural_name)");
            $query->execute(array($single_name, $plural_name, $loot_item_id));

            // Record the item related to the hunt id in hunt_loot relationship table
            $query = $pdo->prepare('INSERT INTO hunt_loot (hunt_id, loot_id, amount, lucky) VALUES (?, ?, ?, ?)');
            $query->execute(array($hunt_id, $loot_item_id, $loot_item['amount'], $lucky));
        }
    }

    // Hunt Details
    if (!empty($_POST['hunt_details']) && !empty($hunt_id)) {
        if (array_key_exists('pillage_type', $_POST['hunt_details']) && array_key_exists('pillage_amount', $_POST['hunt_details'])) {
            $query = $pdo->prepare('INSERT INTO hunt_pillages (hunt_id, pillage_type, amount) values (?, LEFT(?, 1), ?)');
            $query->execute(array($hunt_id, $_POST['hunt_details']['pillage_type'], $_POST['hunt_details']['pillage_amount']));
            $processed_details['pillage_type'] = True;
            $processed_details['pillage_amount'] = True;
        }
        # Process details for the location
        $loc_name = preg_replace("/[^a-z0-9]/", "_", strtolower($_POST['location']['name']));
        $loc_file = "location/" . $loc_name . ".php";
        if (file_exists($loc_file)) {
            include($loc_file);
        }
        foreach ($_POST['hunt_details'] as $detail_type => $detail_value) {
            if (array_key_exists($detail_type, $processed_details)) {
                continue;
            }
            $detail_type_id = 0;
            $detail_value_id = 0;

            $query = $pdo->prepare("SELECT id FROM mhhunthelper.detail_types WHERE name LIKE ?");
            $query->execute(array($detail_type));
            $detail_type_id = $query->fetchColumn();

            if (!$detail_type_id) {
                $query = $pdo->prepare('INSERT INTO mhhunthelper.detail_types (name) VALUES (?)');
                $query->execute(array($detail_type));
                $detail_type_id = $pdo->lastInsertId();
            }

            $query = $pdo->prepare("SELECT id FROM mhhunthelper.detail_values WHERE name LIKE ?");
            $query->execute(array($detail_value));
            $detail_value_id = $query->fetchColumn();

            if (!$detail_value_id) {
                $query = $pdo->prepare('INSERT INTO mhhunthelper.detail_values (name) VALUES (?)');
                $query->execute(array($detail_value));
                $detail_value_id = $pdo->lastInsertId();
            }

            if (!empty($detail_type_id) && !empty($detail_value_id)) {
                $query = $pdo->prepare("INSERT INTO hunt_details (hunt_id, detail_type_id, detail_value_id) VALUES (?, ?, ?)");
                $query->execute(array($hunt_id, $detail_type_id, $detail_value_id));
            }
        }
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Failed transaction: " . $e->getMessage());
}
