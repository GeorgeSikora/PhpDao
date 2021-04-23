<?php

class Car {
    public $id;
    public $type;

    public function __construct($id, $type) {
        $this->id = $id;
        $this->type = $type;
    }
}

class CarDAO extends DAO {

}

?>