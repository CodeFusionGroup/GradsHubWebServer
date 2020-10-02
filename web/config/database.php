<?php 
    class Database {
        private $host;
        private $database_name;
        private $username;
        private $password;

        private $log_obj;

        public $conn;

        function __construct() {
            // get the variables file
            include_once dirname(__FILE__) . "/vars.php";

            // Set the private properties
            $this->host = DB_HOST;
            $this->database_name = DB_NAME;
            $this->username = DB_USERNAME;
            $this->password = DB_PASSWORD;

            // Get the logging file
            include_once $_SERVER['DOCUMENT_ROOT'] . '/class/log.php';
            // Create Log object
            $this->log_obj = new Log();
        }

        public function getConnection(){
            $this->conn = null;
            try{
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database_name, $this->username, $this->password);
                $this->conn->exec("set names utf8");
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(PDOException $exception){
                // Log the database error
                $log_msg = "Database could not be connected: " . $exception->getMessage();
                $this->log_obj->errorLog($log_msg);
                // echo "Database could not be connected: " . $exception->getMessage();
            }
            return $this->conn;
        }
    }  
?>