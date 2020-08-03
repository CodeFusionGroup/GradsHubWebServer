<?php

    class Event{
        // Connection
        private $conn;

        // Table
        private $db_table = "event";

        // Columns
        public $id;
        public $event_id;
        public $title;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }

        // #################### CREATE ####################

        // CREATE
        public function createEvent(){

            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        EVENT_ID = :event_id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->event_id=htmlspecialchars(strip_tags($this->event_id));

            // bind data
            $stmt->bindParam(":event_id", $this->event_id);

            if($stmt->execute()){
               return true;
            }
            return false;

        }
        // CREATE
        public function createUserEvent($query_user_id,$query_like){

            $sqlQuery = "INSERT INTO
                        USER_EVENT
                    SET
                        USER_ID = :user_id,
                        EVENT_ID = :event_id,
                        USER_EVENT_LIKE = :user_like";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $query_user_id=htmlspecialchars(strip_tags($query_user_id));
            $this->id=htmlspecialchars(strip_tags($this->id));
            $query_like=htmlspecialchars(strip_tags($query_like));

            // bind data
            $stmt->bindParam(":user_id", $query_user_id);
            $stmt->bindParam(":event_id", $this->id);
            $stmt->bindParam(":user_like", $query_like);

            if($stmt->execute()){
               return true;
            }
            return false;

        }

        // #################### READ ####################

        public function checkEventLiked($query_user_id){
            $sqlQuery = "SELECT USER_EVENT_ID
                      FROM
                      USER_EVENT
                    WHERE 
                       USER_ID = ? AND EVENT_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $this->id, PDO::PARAM_INT);

            $stmt->execute();
            $stmt_count = $stmt->rowCount();
            if($stmt_count>0){
                return true;
            }else{
                return false;
            }
        }

        // Function retrieves the events a user has voted on
        public function getUserEventVotes($query_user_id){
            $sqlQuery = "SELECT e.EVENT_ID,ue.USER_EVENT_LIKE
                      FROM
                      user_event as ue
                    INNER JOIN ". $this->db_table ." as e
                    ON ue.EVENT_ID = e.ID
                    WHERE 
                       USER_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        }

        // Fucntion checks if an event already exists
        public function checkEventExist(){
            $sqlQuery = "SELECT ID,EVENT_ID
                      FROM
                      ". $this->db_table ."
                    WHERE 
                    EVENT_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $this->event_id, PDO::PARAM_STR);
            

            $stmt->execute();
            $stmt_count = $stmt->rowCount();
            if($stmt_count>0){
                return true;
            }else{
                return false;
            } 
        }

        // Function gets and sets the event id
        public function getEventID(){
            $sqlQuery = "SELECT ID
                      FROM
                      ". $this->db_table ."
                    WHERE 
                    EVENT_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $this->event_id, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
            
        }
    }



?>