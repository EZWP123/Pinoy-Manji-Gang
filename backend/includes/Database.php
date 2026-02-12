<?php
/**
 * Database Helper Class
 */

class Database {
    private $conn;

    public function __construct($host, $user, $pass, $dbname) {
        $this->conn = new mysqli($host, $user, $pass, $dbname);
        
        if ($this->conn->connect_error) {
            die("Connection Error: " . $this->conn->connect_error);
        }
        
        $this->conn->set_charset("utf8mb4");
    }

    // Get connection
    public function getConnection() {
        return $this->conn;
    }

    // Execute query
    public function query($sql) {
        return $this->conn->query($sql);
    }

    // Prepare statement
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    // Escape string
    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }

    // Get last insert ID
    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    // Close connection
    public function close() {
        $this->conn->close();
    }
}
?>
