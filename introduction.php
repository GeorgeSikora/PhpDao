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
    <link rel="stylesheet" href="assets/styles/examples.css"/>

    <link rel="stylesheet" href="assets/styles/main.css">
    <link rel="stylesheet" href="assets/styles/autoMap.css">

    <!-- Highlight.js themes -->
    <link rel="stylesheet" href="assets/styles/highlightjs/vs2015.css">
    <!--<link rel="stylesheet" href="assets/styles/highlightjs/vs.css">-->
    <!--<link rel="stylesheet" href="assets/styles/highlightjs/atelier-estuary.dark.css">-->

    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>

    .image {
        border-radius: 32px; 
        width: 280px;
        padding-bottom: 8px;
    }
    .image.left {
        margin-right: 32px; 
        float: left;
    }
    .image.right {
        margin-left: 32px; 
        float: right;
    }

    </style>
</head>
<body>

<div class="page">

    <?php
        $databaseName = '';
        require 'autoloader.php';
        require 'header.php';
    ?>

<div class="content">
    
    <p class="titleText">O projektu ☄️ <span class="author">23.04.21</span>
    <hr>
    <p class='comment'><img src="assets/images/musk.jpg" class="image left">V PHP jsem si vytvořil univerzální třídu DAO, pomocí které 
    můžeme pracovat s objekty a tabulkami přes vzor, který se vygeneruje zde na stránce. Stačí aby jsme ke každé tabulce v 
    db měli vytvořený objekt se stejnými proměnnými, jak sloupci v tabulce.</p>
    <div style="clear: left"></div>
    
    <p class="titleText">Vývoj 👨🏽‍💻 <span class="author">29.04.21</span>
    <hr>
    <p class='comment'><img src="assets/images/using.jpg" class="image right">
    Intenzivní vývoj v krátké době, když jsme všichni zavření doma na online hodinách má i pozitivní dopady, v některých věcech. 
    Proto jsem začal pracovat na tomto projektu, který má rozveselit každého PHP programátora a usnadnit mu nudné navazování sloupců, 
    které ani nevidíte, ale používáte v PHP. Systém je kompaktní a nemusíte se zbytečně rozpisovat s SQL dotazem, 
    když by jste chtěli náhodou upravovat nějaké záznamy v databázi.</p>
    <div style="clear: right"></div>
    
    <p class="titleText">Flex 😎 <span class="author">29.04.21</span>
    <hr>
    <p class='comment'><img src="assets/images/stonks.jpg" class="image left">
    Používání DAO generátoru zvyšuje účinnost a úhlednost skriptů PHP, ale také reputaci programátorů. Přívětivé to bude nejen pro vás, 
    ocení to i váš tým.</p>
    <div style="clear: left"></div>

    <div id="daoClassCode"></div>

</div>
</div>

    <?php require 'footer.php' ?>

</body>
</html>