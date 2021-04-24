<style>

pre {
    tab-size: 4;
}

</style>


<h3>Automaping</h3>

<?php

require 'autoloader.php';

$tableName = 'users';
$objTableName = substr(ucfirst($tableName), 0, -1);

$con = mysqli_connect("localhost", "root", "", "3bdatabaze") or die("<p class='error'>Chyba s připijením k databázi!</p>");

$result = SQL($con, 
"   SELECT COLUMN_NAME, DATA_TYPE
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = ? 
    AND TABLE_NAME = ?
", ['3bdatabaze', 'users']);
?>

<p>Mapuji MySQL databázi: <b><?= $tableName ?></b></p>

<pre>

<?php

// Class head
echo "&lt;?php<br><br>class $objTableName {<br>";

// Class variables
foreach ($result as $key => $val) {
    echo '&#9;public $'.$val['COLUMN_NAME'].';<br>';
}

// Constructor head
echo "<br>&#9;public function __construct(";
$index = 0;
foreach ($result as $key => $val) {
    $varName = $val['COLUMN_NAME'];
    if ($varName == 'id') continue;
    if ($index != 0) echo ", ";
    echo '$'.$varName.'=';
    switch ($val['DATA_TYPE']) {
        case 'int':
            echo "0";
            break;
        case 'varchar':
            echo "''";
            break;
    }
    $index++;
}
echo ") {<br>";

// Constructor variables handover
foreach ($result as $key => $val) {
    echo '&#9;&#9;$this->'.$val['COLUMN_NAME'].' = ';
    if ($val['COLUMN_NAME'] == 'id') {
        echo '0';
    } else {
        echo '$'.$val['COLUMN_NAME'];
    }
    echo ';<br>';
}

// Constructor and class ending brackets
echo '&#9;}<br>}<br>';

// DAO object class
echo 'class '.$objTableName.'DAO extends DAO {<br><br>}';

// ending php element
echo '<br><br>?>';
?>
</pre>