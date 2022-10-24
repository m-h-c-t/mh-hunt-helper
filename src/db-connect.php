<?php
require_once "config.php";
require_once "db-connect.php";

$pdo;
setPDO();
function setPDO() {
    global $servername, $dbname, $username, $password, $pdo;
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}

$pdo_map;
// setPDOMap();
function setPDOMap() {
    global $mms_servername, $mms_port, $mms_dbname, $mms_username, $mms_password, $pdo_map;
    $pdo_map = new PDO("mysql:host=$mms_servername;port=$mms_port;dbname=$mms_dbname;charset=utf8", $mms_username, $mms_password);
    $pdo_map->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}

$pdo_conv;
// setPDOConv();
function setPDOConv() {
    global $convertible_servername, $convertible_port, $convertible_dbname, $convertible_username, $convertible_password, $pdo_conv;
    $pdo_conv = new PDO("mysql:host=$convertible_servername;port=$convertible_port;dbname=$convertible_dbname;charset=utf8", $convertible_username, $convertible_password);
    $pdo_conv->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}
