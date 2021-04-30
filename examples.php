<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="assets/styles/examples.css"/>
    
    <link rel="stylesheet" href="assets/styles/main.css">
    <link rel="stylesheet" href="assets/styles/autoMap.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/styles/default.min.css">
    <link rel="stylesheet" href="assets/styles/highlightjs/vs2015.css">
    
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAO příklady použití</title>
</head>
<body>

<div class="page">

<?php
$databaseName = '';
require 'autoloader.php';
require 'header.php';
?>

<div class="content">

<p class="titleText">DAO - Vysvětlení s příklady 🧱 <span class="author">23.04.21</span></parse_url>

<hr>

<b>Třída objektu v PHP</b>
<p class='comment'>Tak jak zde, v db máme uložené stejné proměnné, se stejnými názvy.</p>

<pre class='code'><code class="php">class User {
    public $id;
    public $name;
    public $age;

    public function __construct($name='', $age=0) {
        $this->id = 0;
        $this->name = $name;
        $this->age = $age;
    }
}
class UserDAO extends DAO {
    
}
</code></pre>

<hr>

<?php

function echoUser($user) {
    echo "<table class='dataTable'>";
    echo "<tr><th>Id</th><th>Jméno</th><th>Věk</th></tr>";
    if ($user == NULL) {
        echo "<tr colspan='3'>Uživatel neexistuje!</tr>";
    } else {
        echo "<tr><td>$user->id</td><td>$user->name</td><td>$user->age</td></tr>";
    }
    echo "</table>";
}
function echoUsers($users) {
    echo "<table class='dataTable'>";
    echo "<tr><th>Id</th><th>Jméno</th><th>Věk</th></tr>";
    foreach ($users as $index => $user) {
        echo "<tr><td>$user->id</td><td>$user->name</td><td>$user->age</td></tr>";
    }
    echo "</table>";
}

echo "<p><b>Vytvoření DAO instance</b> ~ <i class='code'>\$ud = new UserDAO();</i></p>";
$ud = new UserDAO();
echo "<p class='comment'>DAO - Database Access Object, pomocí této instance komunikujeme s tabulkou</p>";

echo "<hr>";

$id = 2;
$ud->restartAI();
$userToRestart = $ud->getById($id);
$userToRestart->age = 16;
$ud->update($userToRestart);

echo "<p><b>Získání všech objektů</b> ~ <i class='code'>\$ud->getAll();</i></p>";
$users = $ud->getAll();
echoUsers($users);

echo "<hr>";

echo "<p><b>Získání objektu dle id</b> ~ <i class='code'>\$ud->getById(2);</i></p>";
$user = $ud->getById(2);
echoUser($user);

echo "<hr>";

echo "<p><b>Vytvoření nového objektu</b> ~ <i class='code'>\$newId = \$ud->create(new User('Lenka', 19));</i></p>";
$newId = $ud->create(new User('Lenka', 19));
echo "<p class='comment'>\$newId ... $newId</p>";

echo "<hr>";

echo "<p><b>Tabulka po vytvoření nového objektu:</b></p>";
echo "<p class='comment'>Zde můžeme vidět, že do tabulky se nám přidal nový uživatel, tak jak jsme napsali výše v kódu</p>";
$users = $ud->getAll();
echoUsers($users);

echo "<hr>";

echo "<p><b>Aktualizace objektu</b> ~ <i class='code'>\$user->age++; \$ud->update(\$user);</i></p>";
$user->age++;
$ud->update($user);
echoUser($user);

echo "<hr>";

echo "<p><b>Odstranění objektu dle id</b> ~ <i class='code'>\$ud->delete(\$newId);</i></p>";
$ud->delete($newId);
$users = $ud->getAll();
echoUsers($users);

?>

<hr>

<p class='comment'>A to je vše, děkuji za pozornost 🥳</p>

</div>
</div>

<?php require 'footer.php' ?>

</body>
</html>