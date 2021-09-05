<?php

$pdo;
setPDO();
function setPDO() {
    global $servername, $dbname, $username, $password, $pdo;
    // PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}
