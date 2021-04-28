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

    <link rel="stylesheet" href="assets/styles/main.css">
    <link rel="stylesheet" href="assets/styles/autoMap.css">

    <!-- Highlight.js themes -->
    <link rel="stylesheet" href="assets/styles/highlightjs/vs2015.css">
    <!--<link rel="stylesheet" href="assets/styles/highlightjs/vs.css">-->
    <!--<link rel="stylesheet" href="assets/styles/highlightjs/atelier-estuary.dark.css">-->

    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>

<div class="page">

<?php
    $databaseName = '';
    require 'autoloader.php';
    require 'header.php';
?>

<div class="content">
    
    <p class="titleText">Automatické mapování pro MySQL databáze</p>
    
    <hr>

    <table style="width:100%">
        <tr>
            <td style="vertical-align: baseline; width: 40%;">
                
                <label for="databaseName">Název databáze</label><br>
                <!--
                <input 
                    type="text" 
                    id="databaseName" 
                    spellcheck="false" 
                    autocomplete="off"
                >
                -->

                <select id="databaseName">
                    <option disabled selected value>.. vyber databázi ..</option>
                    <?php
                        $con = DAO::dbConnect();
                        $databasesList = SQL($con, "SHOW DATABASES", []);
                        mysqli_close($con);

                        foreach ($databasesList as $index => $db) {
                            $dbName = $db['Database'];
                            echo "<option value='$dbName'>$dbName</option>";
                        }
                    ?>
                </select>

                <br>

                <label for="tableName">Název tabulky</label><br>
                <input 
                    type="text" 
                    id="tableName" 
                    spellcheck="false" 
                    autocomplete="off"
                >
                <br>
                <button class="button" onclick="getTables()">Vygenerovat</button>

            </td>
            <td style="vertical-align: baseline">
                <div id="tablesList"></div>
            </td>
        </tr>
    </table>

    <hr>

    <div id="daoClassCode"></div>

</div>
</div>

    <?php require 'footer.php' ?>

</body>
</html>

<script>

var daoCode;
var databaseName;
var tableName;

$('select#databaseName').on('change', function() {
    getDatabaseTables();
    $('#daoClassCode').html('<p class="info"><i class="fas fa-info"></i> Dále je potřeba zadat název tabulky,<br>nebo kliknout na jednu z dostupných.</p>');
});

function getTables() {
    getDatabaseTables();
    getDaoClassCode();
}
getTables();

function getDatabaseTables(database) {
    databaseName = (database == null) ? $('#databaseName').val() : database;
    $('#tablesList').load('getDatabaseTables.php', { databaseName: databaseName} );
}

function getDaoClassCode(table) {
    tableName = (table == null) ? $('#tableName').val() : table;

    $('#tableName').val(tableName);

    $('#daoClassCode').load('getDaoClassCode.php', { databaseName: databaseName, tableName: tableName}, function() {
    
        daoCode = $('#daoCode').html();
        daoCode = daoCode.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');

        hljs.highlightAll();
    });
}

$(document).on('keypress',function(e) {
    if(e.which == 13) {
        getTables();
    }
});

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

function downloadText(filename, text) {
  var element = document.createElement('a');
  element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
  element.setAttribute('download', filename);

  element.style.display = 'none';
  document.body.appendChild(element);

  element.click();

  document.body.removeChild(element);
}

function copyDaoCode() {
    copyStringToClipboard(daoCode);
    $('#copyDaoCode .tooltiptext').text('Zkopírováno!');
}
function downloadDaoCode() {
    var fileName = tableName.toLowerCase().slice(0, -1) + '.class.php';
    downloadText(fileName, daoCode);
    $('#downloadDaoCode .tooltiptext').text('Staženo!');
}

$("#copyDaoCode").mouseleave(() => {
    $('#copyDaoCode .tooltiptext').text('Kopírovat');
});
$("#downloadDaoCode").mouseleave(() => {
    $('#downloadDaoCode .tooltiptext').text('Stáhnout');
});

</script>