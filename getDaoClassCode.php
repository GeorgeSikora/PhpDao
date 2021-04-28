<?php

require 'class/dao.class.php';

// disable errors and warnings
error_reporting(E_ERROR | E_PARSE);

$databaseName = $_POST['databaseName'];
$tableName = $_POST['tableName']; // user

if ($databaseName == '') {
    die('<p class="info"><i class="fas fa-info"></i> V prvé řadě zadejte název databáze.</p>');
}

if ($tableName == '') {
    die('<p class="info"><i class="fas fa-info"></i> Dále je potřeba zadat název tabulky,<br>nebo kliknout na jednu z dostupných.</p>');
}

$tableNameUniform = substr($tableName, 0, -1); // user
$objTableName = ucfirst($tableNameUniform); // User

$con = mysqli_connect("localhost", "root", "", $databaseName) or die('<p class="error"><i class="fas fa-exclamation-triangle"></i> Chyba s připojením k databázi,<br>nebo je název databáze zadán špatně!</p>');
$result = SQL($con,
"   SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = ? 
    AND TABLE_NAME = ?
", [$databaseName, $tableName]);
$con->close();

if (count($result) == 0) {
    die('<p class="error"><i class="fas fa-exclamation-triangle"></i> Tabulka neexistuje</p>');
}

?>
<p>Zmapovaná tabulka: <b><?=$tableName?></b></p>

<div class="fileName"><?=$tableNameUniform?>.class.php 
    <div id="copyDaoCode" class="tooltip" onclick="copyDaoCode()">
        <i class="far fa-copy"></i>
        <span class="tooltiptext">Kopírovat</span>
    </div>
    <div id="downloadDaoCode" class="tooltip" onclick="downloadDaoCode()">
        <i class="fas fa-file-download"></i>
        <span class="tooltiptext">Stáhnout</span>
    </div>
</div> 

<pre><code id="daoCode" class="php"><?php

// Class head
echo "&lt;?php // $tableNameUniform.class.php&#13;&#13;class $objTableName {&#13;";

// Class variables
foreach ($result as $key => $val) {
    echo '&#9;public $'.$val['COLUMN_NAME'].'; // '.$val['COLUMN_TYPE'].'&#13;';
}

// Constructor head
echo "&#13;&#9;public function __construct(";
$index = 0;
foreach ($result as $key => $val) {
    $varName = $val['COLUMN_NAME'];
    if ($varName == 'id') continue;
    if ($index != 0) echo ", ";
    echo '$'.$varName.'=';
    switch ($val['DATA_TYPE']) {
        case 'int':
        case 'bigint':
        case 'decimal':
            echo "0";
            break;
        case 'varchar':
            echo "''";
            break;
        default:
            echo "null";
            break;
    }
    $index++;
}
echo ") {&#13;";

// Constructor variables handover
foreach ($result as $key => $val) {
    echo '&#9;&#9;$this->'.$val['COLUMN_NAME'].' = ';
    if ($val['COLUMN_NAME'] == 'id') {
        echo '0';
    } else {
        echo '$'.$val['COLUMN_NAME'];
    }
    echo ';&#13;';
}

// Constructor and class ending brackets
echo '&#9;}&#13;}&#13;';

// DAO object class
echo 'class '.$objTableName.'DAO extends DAO {&#13;&#13;}';

// ending php element
echo '&#13;&#13;?>';

?></code></pre>