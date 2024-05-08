<?php

class Batch
{
    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function add($question_id){
        $query = "INSERT INTO response_batches (question_id)
                VALUES ('$question_id')";

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
            return false;

        $query = "UPDATE response_batches SET backup_date = NOW() WHERE id = '$id'";
        $result = mysqli_query($this->conn, $query);
        if ($result)
            return true;
        else
            return false;
    }
    public function existsActive($question_id)
    {
        $query = "SELECT * FROM response_batches WHERE question_id = '$question_id' AND backup_date IS NULL";
        $result = mysqli_query($this->conn, $query);
        if ($result->num_rows >= 1)
            return $result->fetch_assoc()["id"];
        return null;
    }

}