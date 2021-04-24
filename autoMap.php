<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAO Automapping</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/styles/default.min.css">
    <link rel="stylesheet" href="vs2015.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>hljs.highlightAll();</script>


    <script>

    function loadDaoTable (tableName) {
        $('input#tableName').val(tableName);
        $('#tableNameForm').submit();
    }

    </script>


    <style>
    body {
        padding: 16px;
        font-family: FreeMono, monospace;
        color: white;
        background-color: #252526;
    }
    p {
        margin: 0;
    }
    .titleText {
        font-size: 24px;
        color: #eee;
        margin: 0;
    }
    pre {
        tab-size: 4;
    }
    hr {
        border-color: #30382c;
        width: 50%;
        margin-left: -8px;
    }
    .fileName {
        color: #ffa;
        padding-left: 8px;
        line-height: 26px;
        font-size: 16px;
    }
    .error {
        color: #ff4455;
    }
    code {
        font-size: 16px;
        border-radius: 10px;
        padding: 10px 16px !important;
    }
    #copyDaoCode {
        cursor: pointer;
    }

    input[type=text] {
        outline: none;
        border: none;
        color: white;
        font-size: 16px;
        padding: 4px 8px;
        margin: 6px 0;
        border-bottom: 1px solid #0c7d9d;
        background-color: #333333;
    }
 
    /* TOOLTIP */
    .tooltip {
        position: relative;
        display: inline-block;
    }
    .tooltip .tooltiptext {
        font-size: 16px;
        visibility: hidden;
        width: 140px;
        background-color: #000000fa;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 3px 0;
        position: absolute;
        z-index: 1;
        bottom: 105%;
        left: 50%;
        margin-left: -70px;
    }
    .tooltip .tooltiptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: black transparent transparent transparent;
    }
    .tooltip:hover .tooltiptext {
        visibility: visible;
    }

    /* Tables list */
    .tableList {
        margin: 4px 0;
        cursor: pointer;
        text-decoration: underline;
    }

    </style>
</head>
<body>
    
    <p class="titleText">PHP DAO - Automatické mapování pro MySQL</p>
    
    <hr>

    <form action="" method="POST" id="tableNameForm">
        <label for="tableName">Název tabulky</label><br>
        <input 
            type="text" 
            id="tableName" 
            name="tableName" 
            spellcheck="false" 
            autocomplete="off"
            value="<?=isset($_POST['tableName'])?$_POST['tableName']:''?>"
        >
        </form>

    <hr>

    <?php

function getTableList() {
    $con = mysqli_connect("localhost", "root", "", "3bdatabaze") or die("<p class='error'>Chyba s připijením k databázi!</p>");
    $result = SQL($con,
    "   SELECT TABLE_NAME 
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='3bdatabaze' 
    ", ['3bdatabaze', $tableName]);
    $con->close();

    if (count($result) == 0) {
        die('<p class="error">Databáze neobsahuje žádnou tabulku <i class="fas fa-exclamation-triangle"></i></p>');
    }

    echo '<p>Dostupné tabulky:</p>';
    echo '<ul class="tableList">';
    foreach ($result as $key => $val) {
        echo '<li onclick="loadDaoTable(\''.$val['TABLE_NAME'].'\')">'.$val['TABLE_NAME'].'</li>';
    }
    echo '</ul>';
}

    require 'autoloader.php';
    
    getTableList();

    if (isset($_POST['tableName'])) {
        $tableName = $_POST['tableName']; // user
    } else {
        die();
    }

    echo '<hr>';

    $tableNameUniform = substr($tableName, 0, -1); // user
    $objTableName = ucfirst($tableNameUniform); // User

    $con = mysqli_connect("localhost", "root", "", "3bdatabaze") or die("<p class='error'>Chyba s připijením k databázi!</p>");
    $result = SQL($con,
    "   SELECT COLUMN_NAME, DATA_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = ? 
        AND TABLE_NAME = ?
    ", ['3bdatabaze', $tableName]);
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
        echo '&#9;public $'.$val['COLUMN_NAME'].';&#13;';
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
                echo "0";
                break;
            case 'varchar':
                echo "''";
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