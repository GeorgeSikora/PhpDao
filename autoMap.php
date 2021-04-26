<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAO Mapování</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/styles/default.min.css">

    <link rel="stylesheet" href="assets/styles/autoMap.css">

    <!-- Highlight.js themes -->
    <link rel="stylesheet" href="assets/styles/highlightjs/vs2015.css">
    <!--<link rel="stylesheet" href="assets/styles/highlightjs/vs.css">-->
    <!--<link rel="stylesheet" href="assets/styles/highlightjs/atelier-estuary.dark.css">-->

    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>hljs.highlightAll();</script>

    <script>

    function loadDaoTable (tableName) {
        $('input#tableName').val(tableName);
        $('#tableNameForm').submit();
    }

    </script>
</head>
<body>

    <?php
    $databaseName = '';
    require 'autoloader.php';
    ?>
    
    <p class="titleText">PHP DAO - Automatické mapování pro MySQL</p>
    
    <hr>

    <table style="width:100%">
        <tr>
            <td style="vertical-align: baseline; width: 40%;">

                <form action="" method="POST" id="tableNameForm">
                    <label for="databaseName">Název databáze</label><br>
                    <input 
                        type="text" 
                        id="databaseName" 
                        name="databaseName" 
                        spellcheck="false" 
                        autocomplete="off"
                        value="<?=isset($_POST['databaseName'])?$_POST['databaseName']:''?>"
                    >
                    <br>
                    <label for="tableName">Název tabulky</label><br>
                    <input 
                        type="text" 
                        id="tableName" 
                        name="tableName" 
                        spellcheck="false" 
                        autocomplete="off"
                        value="<?=isset($_POST['tableName'])?$_POST['tableName']:''?>"
                    >
                    <br>
                    <button class="button">Vygenerovat</button>
                </form>

            </td>
            <td style="">
                
                <?php
                    if (isset($_POST['databaseName'])) {
                        $databaseName = $_POST['databaseName'];
                    } else {
                        die();
                    }

                    getTableList();
                ?>
            </td>
        </tr>
    </table>


    <?php

function getTableList() {
    global $databaseName;
    $con = mysqli_connect("localhost", "root", "", $databaseName) or die('<p class="error"><i class="fas fa-exclamation-triangle"></i> Chyba s připijením k databázi,<br>nebo je název databáze zadán špatně!</p>');

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

    echo '<p>Dostupné tabulky <span class="refreshButton"><i class="fas fa-sync"></i></span></p>';

    echo '<ul class="tableList">';
    foreach ($result as $key => $val) {
        echo '<li><a onclick="loadDaoTable(\''.$val['TABLE_NAME'].'\')">'.$val['TABLE_NAME'].'</a></li>';
    }
    echo '</ul>';
}

    if (isset($_POST['tableName'])) {
        $tableName = $_POST['tableName']; // user
    } else {
        die();
    }

    echo '<hr>';
    
    if ($tableName == '') {
        die('<p class="info"><i class="fas fa-info"></i> Dále je potřeba zadat název tabulky,<br>nebo kliknout na jednu z dostupných.</p>');
    }

    $tableNameUniform = substr($tableName, 0, -1); // user
    $objTableName = ucfirst($tableNameUniform); // User

    $con = mysqli_connect("localhost", "root", "", $databaseName) or die('<p class="error"><i class="fas fa-exclamation-triangle"></i> Chyba s připijením k databázi,<br>nebo je název databáze zadán špatně!</p>');
    $result = SQL($con,
    "   SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = ? 
        AND TABLE_NAME = ?
    ", [$databaseName, $tableName]);
    $con->close();
    
    if (count($result) == 0) {
        die('<p class="error">Tabulka neexistuje <i class="fas fa-exclamation-triangle"></i></p>');
    }

    ?>

    <p>Zmapovaná tabulka: <b><?=$tableName?></b></p>

    <pre><span class="fileName"><?=$tableNameUniform?>.class.php <div id="copyDaoCode" class="tooltip" onclick="copyDaoCode()"><i class="far fa-copy"></i><span class="tooltiptext">Kopírovat</span></div></span><code id="daoCode" class="php"><?php

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

</body>
</html>

<script>

function loadDaoTable (tableName) {
    $('input#tableName').val(tableName);
    $('#tableNameForm').submit();
}

function copyStringToClipboard(str) {
var el = document.createElement('textarea');
el.value = str;
el.setAttribute('readonly', '');
el.style = {position: 'absolute', left: '-9999px'};
document.body.appendChild(el);
el.select();
document.execCommand('copy');
document.body.removeChild(el);
}

var daoCode = $('#daoCode').html();
daoCode = daoCode.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');

function copyDaoCode() {
    copyStringToClipboard(daoCode);
    $('.tooltiptext').text('Zkopírováno!');
}

$("#copyDaoCode").mouseleave(() => {

    $('.tooltiptext').text('Kopírovat');

});

</script>