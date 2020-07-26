<?php 
    class Database {
        private $host = "us-cdbr-iron-east-04.cleardb.net";
        private $database_name = "heroku_6b7ffb41be0156e";
        private $username = "b6febc76a325a3";
        private $password = "a4831502";

        public $conn;

        // function __construct() {
        //     $url = parse_url(getenv("CLEARDB_DATABASE_URL"));
        //     $this->host = $url["host"];
        //     $this->database_name = substr($url["path"], 1);
        //     $this->username = $url["user"];
        //     $this->password = $url["pass"];
        // }

        public function getConnection(){
            $this->conn = null;
            try{
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database_name, $this->username, $this->password);
                $this->conn->exec("set names utf8");
            }catch(PDOException $exception){
                echo "Database could not be connected: " . $exception->getMessage();
            }
            return $this->conn;
        }
    }  
?>