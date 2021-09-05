<?php

class Database{
    private $conn;
    public function __construct(){
        require "config.php";
        $this->conn = new mysqli($hostname, $username, $password, $db);
        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . mysqli_connect_error() . "<br>");
        }
    }

    public function query(string $sql){
        return $this->conn->query($sql);
    }
}
?>