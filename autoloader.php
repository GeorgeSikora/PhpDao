<?php

error_reporting(E_ERROR | E_PARSE);

foreach (glob('class/*.class.php') as $filename) {
    include_once $filename;
}

?>