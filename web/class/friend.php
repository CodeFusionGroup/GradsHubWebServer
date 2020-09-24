<?php

    class Friend{
        // Connection
        private $conn;

        // Table
        private $db_table = "friend";

        // Columns
        public $id;
        public $user_id;
        public $friend_id;
        public $friend_status;


        // Db connection
        public function __construct(){

            // Get the database.php file
            require_once $_SERVER['DOCUMENT_ROOT'] .'/config/database.php';

            // Create a database object
            $database = new Database();
            $this->conn = $database->getConnection();

        }

        // #################### CREATE ####################

        // Adds a user as a friend
        public function addFriend(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        USER_ID = :user_id,
                        FRIEND_ID = :friend_id,
                        FRIEND_STATUS = 'accepted' ";
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->user_id=htmlspecialchars(strip_tags($this->user_id));
            $this->friend_id=htmlspecialchars(strip_tags($this->friend_id));
            // $this->friend_status=htmlspecialchars(strip_tags($this->friend_status));

            // bind data
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":friend_id", $this->friend_id);
            // $stmt->bindParam(":friend_status", $this->friend_status);

            if($stmt->execute()){
               return true;
            }
            return false;

        }

        // #################### READ ####################

        // Find all friends who have accepted
        public function findAcceptedFriends($query_user_id){
            $sqlQuery = "SELECT u.USER_ID, u.USER_FNAME, u.USER_LNAME 
                    FROM ". $this->db_table ." f
                        INNER JOIN user u ON f.FRIEND_ID = u.USER_ID
                    WHERE f.USER_ID = ? AND f.FRIEND_STATUS = 'accepted' ";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt;

        }

        // Find all friend requests
        public function findFriendRequests($query_user_id){
            $sqlQuery = "SELECT u.USER_ID, u.USER_FNAME, u.USER_LNAME 
                    FROM ". $this->db_table ." f
                        INNER JOIN user u ON f.FRIEND_ID = u.USER_ID
                    WHERE f.USER_ID = ? AND f.FRIEND_STATUS = 'requested' ";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt;

        }

        // Check if a friendship already exists
        public function checkFriendship($query_user_id,$query_friend_id){
            $sqlQuery = "SELECT USER_ID, FRIEND_ID, FRIEND_STATUS 
                    FROM friend
                        WHERE (USER_ID = ? AND FRIEND_ID = ?) 
                            OR (USER_ID = ? AND FRIEND_ID = ?)";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $query_friend_id, PDO::PARAM_INT);
            // OR
            $stmt->bindParam(3, $query_friend_id, PDO::PARAM_INT);
            $stmt->bindParam(4, $query_user_id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt;

        }

        // #################### UPDATE ####################

        // Update a friendship ENUM(accepted,rejected,removed)
        public function updateFriendship(){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        FRIEND_STATUS = :friend_status, 
                    WHERE 
                        USER_ID = :user_ID AND FRIEND_ID = :friend_id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->user_ID=htmlspecialchars(strip_tags($this->user_ID));
            $this->friend_id=htmlspecialchars(strip_tags($this->friend_id));
            $this->friend_status=htmlspecialchars(strip_tags($this->friend_status));
        
            // bind data
            $stmt->bindParam(":user_ID", $this->user_ID);
            $stmt->bindParam(":friend_id", $this->friend_id);
            $stmt->bindParam(":friend_status", $this->friend_status);

        
            if($stmt->execute()){
               return true;
            }
            return false;
        }

    }




?>