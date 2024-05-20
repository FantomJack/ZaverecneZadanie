<?php
class Subject
{
    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }

    // GET -------------------------------------------------------

    public function get(){
        $query = "SELECT * FROM subjects";
        $result = mysqli_query($this->conn, $query);
        $subjects = [];
        while ($row = mysqli_fetch_assoc($result)){
            $subjects[] = $row;
        }
        return $subjects;
    }

    public function getByID($id){
        $query = "SELECT * FROM subjects WHERE id = $id";
        $result = mysqli_query($this->conn, $query);
        $subject = mysqli_fetch_assoc($result);
        return $subject;
    }

    public function getByCode($code){
        $code = strtolower($code);
        $query = "SELECT * FROM subjects WHERE LOWER(code) = '$code'";
        $result = mysqli_query($this->conn, $query);
        $subject = mysqli_fetch_assoc($result);
        return $subject;
    }

    public function getByName($name){
        $name = strtolower($name);
        $query = "SELECT * FROM subjects WHERE LOWER(name) = '$name'";
        $result = mysqli_query($this->conn, $query);
        $subject = mysqli_fetch_assoc($result);
        return $subject;
    }

    // POST -------------------------------------------------------

    public function add($code, $name){
        $query = "INSERT INTO subjects (code, name) VALUES ('$code', '$name')";
        mysqli_query($this->conn, $query);
        return ['id' => mysqli_insert_id($this->conn)];
    }

    // PUT -------------------------------------------------------

    public function update($id, $code, $name){
        $query = "UPDATE subjects SET code = '$code', name = '$name' WHERE id = $id";
        $result = mysqli_query($this->conn, $query);
        if ($result){
            return true;
        }else{
            return false;
        }
    }

    // DELETE -------------------------------------------------------

    public function delete($id){
        $query = "DELETE FROM subjects WHERE id = $id";
        $result = mysqli_query($this->conn, $query);
        if ($result){
            return true;
        }else{
            return false;
        }
    }

}