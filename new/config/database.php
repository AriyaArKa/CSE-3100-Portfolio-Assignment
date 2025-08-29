<?php
class Database
{
    private $host = 'localhost';
    private $dbName = 'portfolio_db';
    private $username = 'root';
    private $password = 'arka'; // Your MySQL password
    private $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbName,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }

    public function createDatabase()
    {
        try {
            $conn = new PDO(
                "mysql:host=" . $this->host,
                $this->username,
                $this->password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "CREATE DATABASE IF NOT EXISTS " . $this->dbName;
            $conn->exec($sql);
            return true;
        } catch (PDOException $e) {
            echo "Error creating database: " . $e->getMessage();
            return false;
        }
    }
}
