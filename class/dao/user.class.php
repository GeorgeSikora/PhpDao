<?php

class User {
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

?>