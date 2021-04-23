<?php

require 'autoloader.php';

function echoUser($user) {
    if ($user == NULL) {
        echo 'Uživatel neexistuje!';
    } else {
        echo "Id: $user->id ... Jméno: $user->name ... Věk: $user->age<br>";
    }
}
function echoUsers($users) {
    foreach ($users as $index => $user) {
        echo "Id: $user->id ... Jméno: $user->name ... Věk: $user->age<br>";
    }
}

echo "<p><b>Vytvoření DAO instance</b> - <i>\$ud = new UserDAO();</i></p>";
$ud = new UserDAO();
echo "DAO - Database Access Object, pomocí této instance komunikujeme s tabulkou";

echo "<p><b>Získání všech objektů</b> - <i>\$ud->getAll();</i></p>";
$users = $ud->getAll();
echoUsers($users);

echo "<p><b>Získání objektu dle id</b> - <i>\$ud->getById(2);</i></p>";
$user = $ud->getById($id = 2);
echoUser($user);

echo "<p><b>Vytvoření nového objektu</b> - <i>\$newId = \$ud->create(new User('Lenka', 19));</i></p>";
$newId = $ud->create(new User('Lenka', 19));
echo "\$newId ... $newId";

echo "<p><b>Tabulka po vytvoření nového objektu:</b></p>";
$users = $ud->getAll();
echoUsers($users);

echo "<p><b>Aktualizace objektu</b> - <i>\$user->age++; \$ud->update(\$user);</i></p>";
$user->age++;
$ud->update($user);
echoUser($user);

echo "<p><b>Odstranění objektu dle id</b> - <i>\$ud->delete(\$newId);</i></p>";
$ud->delete($newId);
$users = $ud->getAll();
echoUsers($users);

?>