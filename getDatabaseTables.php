<?php

require 'class/dao.class.php';

// disable errors and warnings
error_reporting(E_ERROR | E_PARSE);

$databaseName = $_POST['databaseName'];

if ($databaseName == '') {
    die();
}

$con = DAO::dbConnect();

$result = SQL($con, 
"   SELECT TABLE_NAME 
    FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = ? 
    ORDER BY TABLE_NAME
", [$databaseName]);
$con->close();

if (count($result) == 0) {
    die();
}

$tables = [];

foreach ($result as $key => $val) {
    array_push($tables, $val['TABLE_NAME']);
}

$obj = (object)[];
$obj->tables = $tables;
echo json_encode($obj);

?>