<?php

/* Load DAO objects and classes */
foreach (glob('class/dao/*class.php') as $filename) {
    include_once $filename;
}

/* Database Access Object */
class DAO {
    private $className;
    private $tableName;

    public function __construct() {
        $this->className = $this->className();
        $this->tableName = $this->tableName();
    }
    private function dbConnect() {
        return mysqli_connect("localhost", "root", "", "3bdatabaze") or die("<p class='error'>Chyba s připijením k databázi!</p>");
    }
    private function className() {
        return substr(get_class($this), 0, -3);
    }
    private function tableName() {
        return lcfirst($this->className()).'s';
    }

    public function getAll() {
        $class = $this->className;
        $table = $this->tableName;

        $con = $this->dbConnect();
        $sql = "SELECT * FROM $table";
        $query = mysqli_query($con, $sql);
        mysqli_close($con);

        $objs = [];

        while ($dbObj = $query->fetch_assoc()) {
            
            $newObj = new $class();
    
            foreach($newObj as $key => $value) {
                $newObj->$key = $dbObj[$key];
            }

            array_push($objs, $newObj);
        }

        return $objs;
    }
    
    public function getById($id) {
        $class = $this->className;
        $table = $this->tableName;

        $con = $this->dbConnect();
        $sql = "SELECT * FROM $table WHERE id=$id";
        $query = mysqli_query($con, $sql);
        mysqli_close($con);

        $dbObj = mysqli_fetch_assoc($query);
        
        if ($dbObj == null) return null;

        $obj = new $class();

        foreach($obj as $key => $value) {
            $obj->$key = $dbObj[$key];
        }

        return $obj;
    }

    public function create($obj) {
        $table = $this->tableName;

        $sqlMarks = '';
        $sqlData = [];

        $i = 0;
        foreach($obj as $key => $value) {
            array_push($sqlData, $value);
            if ($i != 0) $sqlMarks .= ','; $sqlMarks .= '?'; $i++;
        }

        $sql = "INSERT INTO $table VALUES ($sqlMarks)";

        $con = $this->dbConnect();
        $res = SQL($con, $sql, $sqlData);
        mysqli_close($con);

        return $res;
    }

    public function update($obj) {
        $table = $this->tableName;

        $sqlSets = '';
        $sqlData = [];

        $i = 0;
        foreach($obj as $key => $value) {
            if ($key == 'id') continue;
            array_push($sqlData, $value);
            if ($i != 0) $sqlSets .= ','; 
            $sqlSets .= "$key=?"; 
            
            $i++;
        }
        array_push($sqlData, $obj->id);

        $sql = "UPDATE $table SET $sqlSets WHERE id=?";

        $con = $this->dbConnect();
        $res = SQL($con, $sql, $sqlData);
        mysqli_close($con);

        return $res;
    }
    
    public function delete($id) {
        $table = $this->tableName;

        $sql = "DELETE FROM $table WHERE id=?";

        $con = $this->dbConnect();
        $res = SQL($con, $sql, [$id]);
        mysqli_close($con);

        return $res;
    }


    function restartAI() {
        $table = $this->tableName;

        $sql = "ALTER TABLE $table AUTO_INCREMENT = 1";
        
        $con = $this->dbConnect();
        $res = SQL($con, $sql, []);
        mysqli_close($con);
    }
}


function SQL($con, $sql, $params = []) {

    $stmt = $con->stmt_init();
    $stmt->prepare($sql);
    $typeSign = '';

    for ($i = 0; $i < count($params); $i++) {
        
        $param = $params[$i];
        $type = gettype($param);

        switch($type) {
            case 'string':  $typeSign .= 's'; break;
            case 'integer': $typeSign .= 'i'; break;
            case 'double':  $typeSign .= 'd'; break;
            case 'boolean': $typeSign .= 's'; $params[$i] = $param?'true':'false'; break;
            case 'NULL': $typeSign .= 's'; break;
            default: die('Špatný parametr .. ' . $type);
        }
    }

    if ($stmt->errno != null) {
        die('<br>V syntaxi SQL došlo k chybě ' . $stmt->errno . '<br>SQL: <b>' . $sql . '</b>');
    }

    if (count($params) > 0) {
        $stmt->bind_param($typeSign, ...$params);
    }
    
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result == null) {

        $statement = strtoupper(explode(' ',trim($sql))[0]);

        if ($statement == 'UPDATE' || $statement == 'DELETE') {
            return $stmt->affected_rows;
        }
        if ($statement == 'INSERT') {
            return $stmt->insert_id;
        }

        return -1;
    }

    $arrayResult = array();

    while ($row = $result->fetch_assoc()) {
        array_push($arrayResult, $row);
    }

    $stmt->close();

    return $arrayResult;
}

?>