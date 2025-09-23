<?php
/**
 * Database Connection
 */
class Database {
    private static $instance = null;
    private $conn;
    
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '123456';  // Set your database password if needed
    private $dbname = 'webblog223';
    
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->user,
                $this->pass,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                )
            );
            // Add connection success message for debugging
            // file_put_contents('debug_db.log', 'Connected to database successfully' . PHP_EOL, FILE_APPEND);
        } catch(PDOException $e) {
            // Log detailed error for debugging
            file_put_contents('debug_db.log', 'Connection Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
            echo 'Connection Error: ' . $e->getMessage();
        }
    }
    
    // Singleton pattern to ensure only one connection instance
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
}
