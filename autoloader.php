<?php

foreach (glob('class/*.class.php') as $filename) {
    include_once $filename;
}

?>