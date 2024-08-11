<?php

namespace Chattermax\DB;

use PDO;
use PDOException;
use Chattermax\Config\Config;

class Database {
    protected $conn;

    public function __construct() {
        Config::init();
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . Config::get('DB_HOST') . ";dbname=" . Config::get('DB_NAME'), Config::get('DB_USER'), Config::get('DB_PASS'));
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
//            echo "Connected to database successfully.";
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
    }

    // Method to check if a table exists
    public function tableExists($tableName) {
        try {
            $result = $this->conn->query("SELECT 1 FROM $tableName LIMIT 1");
            return $result !== false;
        } catch (PDOException $e) {
//            echo "Error checking table existence: " . $e->getMessage();
            return false;
        }
    }
}
