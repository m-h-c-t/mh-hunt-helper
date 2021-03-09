<?php

require_once "config.php";
global $pdo, $servername, $dbname, $username, $password, $port;
if (!$pdo) {
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}

if (!empty($code_names_only)) {
    $filters_query = 'SELECT code_name FROM filters ORDER BY sort';
    $filters_query = $pdo->prepare($filters_query);
    $filters_query->execute();
    $filters = $filters_query->fetchAll(PDO::FETCH_COLUMN, 0);
}
else {
    $filters_query = 'SELECT * FROM filters ORDER BY sort';
    $filters_query = $pdo->prepare($filters_query);
    $filters_query->execute();
    $filters = $filters_query->fetchAll(PDO::FETCH_ASSOC);
}


if (!isset($silent) || !$silent) {
    header('Content-Type: application/json');
    print json_encode($filters, JSON_PRETTY_PRINT);
}
