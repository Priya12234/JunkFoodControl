<?php
class Database
{
    private $host = "localhost";
    private $db_name = "junk_food_tracker";
    private $username = "root";   // change if needed
    private $password = "";       // change if needed
    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Optional: set UTF-8 for proper encoding
            $this->conn->exec("set names utf8");
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }

        return $this->conn;
    }
}
