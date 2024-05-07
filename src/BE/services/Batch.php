<?php

class Question
{
    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function add($question_id){
        $query = "INSERT INTO response_batches (question_id)
                VALUES ('$question_id')";

        $result = mysqli_query($this->conn, $query);
        if ($result){
            return true;
        }else{
            return false;
        }
    }
    public function backup($id)
    {

    }
    public function backupQuestion($question_id)
    {

    }
    public function existsActive($question_id)
    {

    }

    public function current($question_id){

    }

}