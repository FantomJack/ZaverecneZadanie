<?php

class Question
{
    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }
    public function exists($batch_id, $answer){
        $query = "SELECT * FROM responses WHERE batch_id = '$batch_id' AND answer = '$answer'";
        $result = $this->conn->query($query);

        $id = null;
        if ($result->num_rows == 1) $id = mysqli_fetch_array($result)['id'];

        return $id;
    }
    public function add($batch_id, $answer): bool{
        $id = $this->exists($batch_id, $answer);
        if($id) return $this->vote($id);

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