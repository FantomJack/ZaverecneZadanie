<?php
class Subject
{
    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }


    public function get(){
        $query = "GET * FROM subjects";
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

    public function add($code, $name){
        $query = "INSERT INTO subjects (code, name) VALUES ('$code', '$name')";
        $result = mysqli_query($this->conn, $query);
        if ($result){
        return true;
        }else{
            return false;
        }
    }

    public function update($id, $code, $name){
        $query = "UPDATE subjects SET code = '$code', name = '$name' WHERE id = $id";
        $result = mysqli_query($this->conn, $query);
        if ($result){
            return true;
        }else{
            return false;
        }
    }

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