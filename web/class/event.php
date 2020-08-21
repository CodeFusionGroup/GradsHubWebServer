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
        public function createUserEvent($query_user_id,$query_vote){

            $sqlQuery = "INSERT INTO
                        user_vote
                    SET
                        USER_ID = :user_id,
                        EVENT_ID = :event_id,
                        USER_EVENT_VOTE = :user_vote";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $query_user_id=htmlspecialchars(strip_tags($query_user_id));
            $this->id=htmlspecialchars(strip_tags($this->id));
            $query_vote=htmlspecialchars(strip_tags($query_vote));

            // bind data
            $stmt->bindParam(":user_id", $query_user_id);
            $stmt->bindParam(":event_id", $this->id);
            $stmt->bindParam(":user_vote", $query_vote);

            if($stmt->execute()){
               return true;
            }
            return false;

        }

        // This function creates a new favourited event
        public function insertFavouriteEvent($query_user_id){
            $sqlQuery = "INSERT INTO
                        user_favourite
                    SET
                        USER_ID = :user_id,
                        EVENT_ID = :event_id,
                        USER_EVENT_FAVOURITE = 'true'";
        
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $query_user_id=htmlspecialchars(strip_tags($query_user_id));
            $this->id=htmlspecialchars(strip_tags($this->id));
           
            // bind data
            $stmt->bindParam(":user_id", $query_user_id);
            $stmt->bindParam(":event_id", $this->id);

            if($stmt->execute()){
                return true;
            }
            return false;
        }

        // #################### READ ####################

        // This function checks if the user has already voted for the event
        public function checkEventLiked($query_user_id){
            $sqlQuery = "SELECT USER_VOTE_ID
                      FROM
                      user_vote
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
        // This function checks if the user has already favourited an event
        public function checkEventFavourite($query_user_id){
            $sqlQuery = "SELECT USER_FAVOURITE_ID
                      FROM
                      user_favourite
                    WHERE 
                       USER_ID = ? AND EVENT_ID = ? AND USER_EVENT_FAVOURITE = 'true'";
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
            $sqlQuery = "SELECT e.EVENT_ID,uv.USER_EVENT_VOTE
                      FROM
                      user_vote as uv
                    INNER JOIN ". $this->db_table ." as e
                    ON uv.EVENT_ID = e.ID
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

        // Function gets the event id
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
        
        
        //fetchs all events a user favourited
         public function getUserEventFavourite($query_user_id){
         $sqlQuery = "SELECT e.EVENT_ID
                      FROM
                       user_favourite as uf
                       INNER JOIN event as e 
                       ON uf.EVENT_ID=e.ID
                    WHERE 
                       USER_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        }

        // Function fetches all the events with its votes
        public function fetchAll(){
            $sqlQuery = "SELECT e.EVENT_ID,
                COUNT(IF(uv.USER_EVENT_VOTE = 'true',uv.USER_VOTE_ID,NULL)) AS VOTES_TRUE,
                COUNT(IF(uv.USER_EVENT_VOTE = 'false',uv.USER_VOTE_ID,NULL)) AS VOTES_FALSE
                FROM user_vote uv
                INNER JOIN ". $this->db_table ." e
                ON uv.EVENT_ID = e.ID
                GROUP BY e.EVENT_ID;";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();
            return $stmt;
            
        }
    }



?>
