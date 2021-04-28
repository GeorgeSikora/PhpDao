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
    die('<p class="error"><i class="fas fa-exclamation-triangle"></i> Databáze neobsahuje žádnou tabulku</p>');
}
?>
<p>Dostupné tabulky 
    <span class="refreshButton tooltip" onclick="getDatabaseTables()">
        <i class="fas fa-sync"></i>
        <span class="tooltiptext">Obnovit obsah</span>
    </span>
</p>

<?php
echo '<ul class="tableList">';
foreach ($result as $key => $val) {
    echo '<li><a onclick="getDaoClassCode(\''.$val['TABLE_NAME'].'\')">'.$val['TABLE_NAME'].'</a></li>';
}
echo '</ul>';

?>