<?php

class Response
{
    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function get(){
        $query = "SELECT * FROM responses";
        $result = mysqli_query($query);
        $responses = [];
        while ($row = mysqli_fetch_assoc($result)){
            $responses[] = $row;
        }
        return $responses;
    }

    public function getBatch($batch_id){
        $query = "SELECT * FROM responses WHERE batch_id = $batch_id";
        $result = mysqli_query($this->conn, $query);
        $responses = [];
        while ($row = mysqli_fetch_assoc($result)){
            $responses[] = $row;
        }
        return $responses;
    }

    public function getCurrentAnswers($question_id){
        $query = "SELECT * FROM response_batches rb LEFT JOIN responses r ON rb.id = r.batch_id
         WHERE question_id = $question_id AND backup_date IS NULL";
        $result = mysqli_query($this->conn, $query);
        $responses = [];
        while ($row = mysqli_fetch_assoc($result)){
            $responses[] = $row;
        }
        return $responses;
    }

    public function getByID($id){
        $query = "SELECT * FROM responses WHERE id = $id";
        $result = mysqli_query($this->conn, $query);
        $response = mysqli_fetch_assoc($result);
        return $response;
    }

    public function exists($batch_id, $answer){
        $query = "SELECT * FROM responses WHERE batch_id = '$batch_id' AND answer = '$answer'";
        $result = $this->conn->query($query);

        $id = null;
        if ($result->num_rows == 1) $id = mysqli_fetch_array($result)['id'];

        return $id;
    }
    public function add($batch_id, $answer): bool{
//        $id = $this->exists($batch_id, $answer);
//        if($id) return $this->vote($id);

        $query = "INSERT INTO responses (batch_id, answer, votes)
                VALUES ('$batch_id', '$answer', '0')";

        $result = mysqli_query($this->conn, $query);
        if ($result){
            return true;
        }else{
            return false;
        }
    }
    public function vote($id): bool
    {
        $query = "UPDATE responses
            SET votes = votes + 1
            WHERE id = '$id';";
        $result = mysqli_query($this->conn, $query);
        if ($result){
            return true;
        }else{
            return false;
        }
    }
    public function update($id, $answer): bool
    {
        $query = "UPDATE responses SET answer = '$answer' WHERE id = '$id';";
        $result = mysqli_query($this->conn, $query);
        if ($result){
            return true;
        }else{
            return false;
        }
    }
    public function zero($id){
        $query = "UPDATE responses SET votes = 0 WHERE id = '$id';";
        $result = mysqli_query($this->conn, $query);
        if ($result){
            return true;
        }else{
            return false;
        }
    }
    public function delete($id){
        $query = "DELETE FROM responses WHERE id = '$id';";
        $result = mysqli_query($this->conn, $query);

//        $stmt = mysqli_prepare($this->conn, $query);
//        $stmt->bind_param('i',$receiver_id);

//        if ($stmt->execute()) {
        if ($result){
            return true;
        } else {
            return false;
        }
    }

}