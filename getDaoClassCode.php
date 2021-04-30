<?php

require 'class/dao.class.php';

// disable errors and warnings
error_reporting(E_ERROR | E_PARSE);

$databaseName = $_POST['databaseName'];
$tableName = $_POST['tableName']; // user

if ($databaseName == '') {
    die();
}

if ($tableName == '') {
    //die('<p class="info"><i class="fas fa-info"></i> Dále je potřeba zadat název tabulky,<br>nebo kliknout na jednu z dostupných.</p>');
    die();
}

$classNameUniform = substr($tableName, 0, -1); // user
$className = ucfirst($classNameUniform); // User

$con = DAO::dbConnect();
$result = SQL($con,
"   SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = ? 
    AND TABLE_NAME = ?
", [$databaseName, $tableName]);
$con->close();

if (count($result) == 0) {
    /*
    $obj = (object)[];
    $obj->response = 'error';
    $obj->error = 'tableNotExists';
    echo json_encode($obj);
    */
    die();
    //die('<p class="error"><i class="fas fa-exclamation-triangle"></i> Tabulka neexistuje</p>');
}

$classCode = '';

// Class head
$classCode .= "&lt;?php // $classNameUniform.class.php&#13;&#13;class $className {&#13;";

// Class variables
foreach ($result as $key => $val) {
    $classCode .= '&#9;public $'.$val['COLUMN_NAME'].'; // '.$val['COLUMN_TYPE'].'&#13;';
}

// Constructor head
$classCode .= "&#13;&#9;public function __construct(";
$index = 0;
foreach ($result as $key => $val) {
    $varName = $val['COLUMN_NAME'];
    if ($varName == 'id') continue;
    if ($index != 0) $classCode .= ", ";
    $classCode .= '$'.$varName.'=';
    switch ($val['DATA_TYPE']) {
        case 'int':
        case 'bigint':
        case 'decimal':
            $classCode .= "0";
            break;
        case 'varchar':
            $classCode .= "''";
            break;
        default:
            $classCode .= "null";
            break;
    }
    $index++;
}
$classCode .= ") {&#13;";

// Constructor variables handover
foreach ($result as $key => $val) {
    $classCode .= '&#9;&#9;$this->'.$val['COLUMN_NAME'].' = ';
    if ($val['COLUMN_NAME'] == 'id') {
        $classCode .= '0';
    } else {
        $classCode .= '$'.$val['COLUMN_NAME'];
    }
    $classCode .= ';&#13;';
}

// Constructor and class ending brackets
$classCode .= '&#9;}&#13;}&#13;';

// DAO object class
$classCode .= 'class '.$className.'DAO extends DAO {&#13;&#13;}';

// ending php element
$classCode .= '&#13;&#13;?>';

$obj = (object)[];
$obj->response = 'ok';
$obj->className = $classNameUniform;
$obj->classCode = $classCode;
echo json_encode($obj);

?>