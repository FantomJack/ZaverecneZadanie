<?php

class Question
{
    private $conn;
    private $code = ['a','b','c','d','e','f','g','h','i','j',
        'k','l','m','n','o','p','q','r','s','t',
        'u','v','w','x','y','z'];
    public function __construct($conn){
        $this->conn = $conn;
    }
    private function hash($id_part){
        if($id_part < 10) return $id_part;
        elseif ($id_part - 10 < 26) return $this->code[$id_part - 10];
        elseif ($id_part - 36 < 26) return strtoupper($this->code[$id_part - 36]);
        return null;
    }
    //returns null on failure
    private function generateQRCode($id): ?string
    {
        $p5 = $this->hash($id%62);
        $share = intdiv($id,62);
        $p4 = $this->hash($share%62);
        $share = intdiv($share,62);
        $p3 = $this->hash($share%62);
        $share = intdiv($share,62);
        $p2 = $this->hash($share%62);
        $share = intdiv($share,62);
        $p1 = $this->hash($share%62);

        $qrcode = $p1.$p2.$p3.$p4.$p5;

        $query = "UPDATE questions SET code = '$qrcode' WHERE id = $id";
        $result = mysqli_query($this->conn, $query);
        if ($result){
            return $qrcode;
        }else return null;
    }
    public function getQRCode($id){
        $qrcode = null;

        $query = "SELECT * FROM questions WHERE id = $id";
        $result = mysqli_query($this->conn, $query);
        if ($result->num_rows == 1)
            $qrcode = mysqli_fetch_assoc($result)["code"];
        return $qrcode;
    }

    public function getByQRCode($QRCode)
    {
        $query = "SELECT * FROM questions WHERE code = $QRCode AND closed_at IS NULL";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    public function getByOwner($owner_id): bool|array|null
    {
        $query = "SELECT * FROM questions WHERE owner_id = $owner_id";
        $result = mysqli_query($this->conn, $query);
        $questions = [];
        while ($row = mysqli_fetch_assoc($result)){
            $questions[] = $row;
        }
        return $questions;
    }
    public function get(): bool|array|null
    {
        $query = "SELECT * FROM questions";
        $result = mysqli_query($this->conn, $query);
        $questions = [];
        while ($row = mysqli_fetch_assoc($result)){
            $questions[] = $row;
        }
        return $questions;
    }

    public function add($subject_id, $owner_id, $text, $type, $end_date): array
    {

        $query = "INSERT INTO `receivers` (`subject_id`, `owner_id`, `text`, `type`, `end_date`)
                VALUES (?,?,?,?,?)";

        $stmt = mysqli_prepare($this->conn, $query);
        $stmt->bind_param('iisss',$country_id,$name,$surname,$organisation,$sex,$birth,$death);
        $stmt->execute();
        $id = mysqli_insert_id($this->conn);
        $qrcode = $this->generateQRCode($id);

        return array($id,$qrcode);
    }

    public function close($id)
    {
        $query = "UPDATE questions SET closed_at = NOW() WHERE id = $id";
        $result = mysqli_query($this->conn, $query);
        if ($result)
            return true;
        else
            return false;
    }

    public function isActive($id): bool
    {
        $query = "SELECT * FROM questions WHERE id = $id";
        $result = mysqli_query($this->conn, $query);
        if ($result->num_rows == 1)
            if(mysqli_fetch_assoc($result)["closed_at"] != null)
                return true;
        return false;
    }

    public function isQRCodeActive($qrcode): bool
    {
        $query = "SELECT * FROM questions WHERE code = '$qrcode' AND closed_at IS NOT NULL";
        $result = mysqli_query($this->conn, $query);
        if ($result->num_rows == 1)
            return true;
        return false;
    }

    public function delete($id): bool
    {
        $query = "DELETE FROM questions WHERE id = $id";
        $result = mysqli_query($this->conn, $query);
        if ($result)
            return true;
        else
            return false;
    }
    public function deleteByQRCode($qrcode): bool
    {
        $query = "DELETE FROM questions WHERE code = $qrcode";
        $result = mysqli_query($this->conn, $query);
        if ($result)
            return true;
        else
            return false;
    }


}