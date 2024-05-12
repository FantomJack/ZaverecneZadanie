<?php

class Batch
{
    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function get()
    {
        $query = "SELECT * FROM response_batches";
        $result = mysqli_query($this->conn, $query);
        $batches = [];
        while ($row = mysqli_fetch_assoc($result)){
            $batches[] = $row;
        }
        return $batches;
    }
    public function getByQuestion($question_id): array
    {
        $query = "SELECT * FROM response_batches WHERE question_id = '$question_id'";
        $result = mysqli_query($this->conn, $query);
        $batches = [];
        while ($row = mysqli_fetch_assoc($result)){
            $batches[] = $row;
        }
        return $batches;
    }


    public function add($question_id, $name){
        $query = "INSERT INTO response_batches (question_id, name)
                VALUES ('$question_id', '$name')";

        $result = mysqli_query($this->conn, $query);
        return mysqli_insert_id($this->conn);
    }
    public function backup($id)
    {
        $query = "UPDATE response_batches SET backup_date = NOW() WHERE id = '$id'";
        $result = mysqli_query($this->conn, $query);
        if ($result)
            return true;
        else
            return false;
    }
    public function backupQuestion($question_id)
    {
        $id = $this->existsActive($question_id);
        if ($id == null)
            return true;

        $query = "UPDATE response_batches SET backup_date = NOW() WHERE id = '$id'";
        $result = mysqli_query($this->conn, $query);
        if ($result)
            return true;
        else
            return false;
    }
    public function update($id, $name): bool
    {
        $query = "UPDATE response_batches SET name = '$name' WHERE id = '$id';";
        $result = mysqli_query($this->conn, $query);
        if ($result){
            return true;
        }else{
            return false;
        }
    }

    public function existsActive($question_id)
    {
        $query = "SELECT * FROM response_batches WHERE question_id = '$question_id' AND backup_date IS NULL";
        $result = mysqli_query($this->conn, $query);
        if ($result->num_rows >= 1)
            return $result->fetch_assoc()["id"];
        return null;
    }

    public function delete($id){
        $query = "DELETE FROM response_batches WHERE id = '$id'";
        $result = mysqli_query($this->conn, $query);
        if ($result)
            return true;
        else
            return false;
    }

}