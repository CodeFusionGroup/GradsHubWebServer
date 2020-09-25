<?php 
    class Blocked{

        // Connection
        private $conn;

        // Table
        private $db_table = "blocked";

        // Columns
        public $id;
        public $user_id;
        public $blocked_user_id;

        // Db connection
        public function __construct(){

            // Get the database.php file
            require_once $_SERVER['DOCUMENT_ROOT'] .'/config/database.php';

            // Create a database object
            $database = new Database();
            $this->conn = $database->getConnection();

        }

        // #################### CREATE ####################

        public function blockUser(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        USER_ID = :user_id,
                        BLOCKED_USER_ID = :blocked_user_id";
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->user_id=htmlspecialchars(strip_tags($this->user_id));
            $this->blocked_user_id=htmlspecialchars(strip_tags($this->blocked_user_id));

            // bind data
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":blocked_user_id", $this->blocked_user_id);

            if($stmt->execute()){
               return true;
            }
            return false;
        }

        // #################### READ ####################

        // Find all blocked users
        public function findBlockedUsers($query_user_id){
            $sqlQuery = "SELECT u.USER_ID, u.USER_FNAME, u.USER_LNAME 
                    FROM ". $this->db_table ." b
                        INNER JOIN user u ON b.BLOCKED_USER_ID = u.USER_ID
                    WHERE b.USER_ID = ? AND BLOCKED_STATUS = 'true' ";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt;
        }

        // Check if a user has been blocked
        public function checkBlocked($query_user_id,$blocker_user_id){
            $sqlQuery = "SELECT BLOCKED_ID 
                    FROM ". $this->db_table ."
                    WHERE USER_ID = ? AND BLOCKED_USER_ID = ? AND BLOCKED_STATUS = 'true' ";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $blocker_user_id, PDO::PARAM_INT);
            

            $stmt->execute();
            $stmt_count = $stmt->rowCount();

            if($stmt_count > 0){
                return true;
            }
            return false;
        }

        // #################### UPDATE ####################

        // Unblock a user
        public function unblockUser(){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        BLOCKED_STATUS = 'false'
                    WHERE 
                        USER_ID = :user_id AND BLOCKED_USER_ID = :blocked_user_id ";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->user_id=htmlspecialchars(strip_tags($this->user_id));
            $this->blocked_user_id=htmlspecialchars(strip_tags($this->blocked_user_id));
        
            // bind data
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":blocked_user_id", $this->blocked_user_id);
        
            if($stmt->execute()){
               return true;
            }
            return false;
        }


        // #################### DELETE ####################

        // Unblock a user
        public function unblockUser2(){
            $sqlQuery = "DELETE FROM " . $this->db_table . " 
                    WHERE USER_ID = ? AND BLOCKED_USER_ID = ?";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // Sanitize
            $this->user_id=htmlspecialchars(strip_tags($this->user_id));
            $this->blocked_user_id=htmlspecialchars(strip_tags($this->blocked_user_id));
        
            // bind data
            $stmt->bindParam(1, $this->user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $this->blocked_user_id, PDO::PARAM_INT);
        
            if($stmt->execute()){
               return true;
            }
            return false;
        }
    }
?>