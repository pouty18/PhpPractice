<?php
require_once ('functions.inc');
class MySQLDao {
    var $conn = null;
    var $result = null;

    function __construct() {
    }

    public function openConnection() {
        $this->conn = new mysqli(DBHOST, DBUSER, DBPASS, DB);
        if (mysqli_connect_errno())
            echo new Exception("Could not establish connection with database");
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn != null)
            $this->conn->close();
    }

    public function getUserDetails($email)
    {
        $returnValue = array();
        $sql = "select * from users where user_email='{$email}'";

        $result = $this->conn->query($sql);
        if ($result != null && (mysqli_num_rows($result) >= 1)) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            if (!empty($row)) {
                $returnValue = $row;
            }
        }
        return $returnValue;
    }

    public function getUserDetailsWithPassword($email, $userPassword)
    {

        $returnValue = array();
        $sql = "select id,user_email from users where user_email='{$email}' and user_password='{$userPassword}'";

        $result = $this->conn->query($sql);
        if ($result != null && (mysqli_num_rows($result) >= 1)) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            if (!empty($row)) {
                $returnValue = $row;
            }
        }
        return $returnValue;
    }

    public function registerUser($email, $password)
    {
        $sql = "insert into users set user_email=?, user_password=?";
        $statement = $this->conn->prepare($sql);

        if (!$statement)
            throw new Exception($statement->error);

        $statement->bind_param("ss", $email, $password);
        $returnValue = $statement->execute();

        return $returnValue;
    }

}
?>
