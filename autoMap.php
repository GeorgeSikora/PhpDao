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
                <p>Dostupné tabulky 
                    <span class="refreshButton tooltip" onclick="getDatabaseTables()">
                        <i class="fas fa-sync"></i>
                        <span class="tooltiptext">Obnovit obsah</span>
                    </span> <span id="tablesCount" style="color: #aaa;"></span>
                </p>
                <div id="tablesList"></div>
            </td>
        </tr>
    </table>

    <hr>

    <div id="daoClassCodeDiv">
        <p class="info"><i class="fas fa-info"></i> V prvé řadě zvolte databázi.</p>
    </div>

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
    $('#daoClassCodeDiv').html('<p class="info"><i class="fas fa-info"></i> Dále je potřeba zadat název tabulky,<br>nebo kliknout na jednu z dostupných.</p>');
});

function getTables() {
    getDatabaseTables();
    getDaoClassCode();
}
//getTables();
/*
function getDatabaseTables(database) {
    databaseName = (database == null) ? $('#databaseName').val() : database;
    $('#tablesList').load('getDatabaseTables.php', { databaseName: databaseName} );
}
*/
function getDatabaseTables(database) {
    databaseName = (database == null) ? $('#databaseName').val() : database;
    $.ajax({
        type: 'POST',
        url: 'getDatabaseTables.php',
        data: {databaseName: databaseName},
        success: function(data) {
            var result;
            try {
                result = JSON.parse(data);
            } catch(e) {
                $('#tablesList').html('<p class="error"><i class="fas fa-exclamation-triangle"></i> Databáze neobsahuje žádnou tabulku</p>');
                return;
            }

            $('#tablesList').empty();
            const tables = result.tables;
            const ul = $("<ul>", {class: "tableList"});

            for (var i = 0; i < tables.length; i++) {
                const table = tables[i];
                ul.append(`<li><a onclick="getDaoClassCode('${table}')">${table}</a></li>`);
            }
            $('#tablesList').append(ul);

            const tl = tables.length;

            if (tl == 0) {
                $('#tablesCount').html(`(Žádná tabulka)`);
            } else if (tl == 1) {
                $('#tablesCount').html(`(${tl} tabulka)`);
            } else if (tl < 5) {
                $('#tablesCount').html(`(${tl} tabulky)`);
            } else {
                $('#tablesCount').html(`(${tl} tabulek)`);
            }
        }
    });
}

function getDaoClassCode(table) {
    tableName = (table == null) ? $('#tableName').val() : table;

    $('#tableName').val(tableName);

    $.ajax({
        type: 'POST',
        url: 'getDaoClassCode.php',
        data: {databaseName: databaseName, tableName: tableName},
        success: function(data) {
            var result;

            try {
                result = JSON.parse(data);
            } catch(e) {
                $('#daoClassCodeDiv').html('<p class="error"><i class="fas fa-exclamation-triangle"></i> Tabulka neexistuje</p>');
                return;
            }

            const className = result.className;

            var daoClassCode = `
            <p>Zmapovaná tabulka: <b>${tableName}</b></p>
            <div class="fileName">${className}.class.php 
                <div id="copyDaoCode" class="tooltip" onclick="copyDaoCode()">
                    <i class="far fa-copy"></i>
                    <span class="tooltiptext">Kopírovat</span>
                </div>
                <div id="downloadDaoCode" class="tooltip" onclick="downloadDaoCode()">
                    <i class="fas fa-file-download"></i>
                    <span class="tooltiptext">Stáhnout</span>
                </div>
            </div> 

            <pre><code id="daoClassCode" class="php">`;

            daoClassCode += result.classCode;
            daoClassCode += '</pre></code>';
            
            $('#daoClassCodeDiv').html(daoClassCode);
            
            daoCode = $('#daoClassCode').html();
            daoCode = daoCode.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');

            hljs.highlightAll();
        }
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