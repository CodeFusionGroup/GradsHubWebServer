<?php 
    class Database {
        private $host;
        private $database_name;
        private $username;
        private $password;

        public $conn;

        function __construct() {
            // get the variables file
            include_once dirname(__FILE__) . "/vars.php";

            // Set the private properties
            $this->host = DB_HOST;
            $this->database_name = DB_NAME;
            $this->username = DB_USERNAME;
            $this->password = DB_PASSWORD;
        }

        public function getConnection(){
            $this->conn = null;
            try{
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database_name, $this->username, $this->password);
                $this->conn->exec("set names utf8");
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(PDOException $exception){
                echo "Database could not be connected: " . $exception->getMessage();
            }
            return $this->conn;
        }
    }  
?>