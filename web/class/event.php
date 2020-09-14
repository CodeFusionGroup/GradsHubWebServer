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
        public function __construct(){
            // Get the database.php file
            require_once $_SERVER['DOCUMENT_ROOT'] .'/config/database.php';

            // Create a database object
            $database = new Database();
            $this->conn = $database->getConnection();
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
        
        // CREATE an new event vote(REMOVED)
        // public function createEventVote($query_user_id,$query_vote){

        //     $sqlQuery = "INSERT INTO
        //                 event_vote
        //             SET
        //                 USER_ID = :user_id,
        //                 EVENT_ID = :event_id,
        //                 EVENT_VOTE = :user_vote";
        
        //     $stmt = $this->conn->prepare($sqlQuery);
        
        //     // sanitize
        //     $query_user_id=htmlspecialchars(strip_tags($query_user_id));
        //     $this->id=htmlspecialchars(strip_tags($this->id));
        //     $query_vote=htmlspecialchars(strip_tags($query_vote));

        //     // bind data
        //     $stmt->bindParam(":user_id", $query_user_id);
        //     $stmt->bindParam(":event_id", $this->id);
        //     $stmt->bindParam(":user_vote", $query_vote);

        //     if($stmt->execute()){
        //        return true;
        //     }
        //     return false;

        // }

        // This function creates a new favourited event
        public function createFavouriteEvent($query_user_id){
            $sqlQuery = "INSERT INTO
                        event_favourite
                    SET
                        USER_ID = :user_id,
                        EVENT_ID = :event_id,
                        EVENT_FAVOURITE = 'true'";
        
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

        // This function checks if the user has already voted for the event(REMOVED)
        // public function checkEventLiked($query_user_id){
        //     $sqlQuery = "SELECT EVENT_VOTE_ID
        //               FROM
        //               event_vote
        //             WHERE 
        //                USER_ID = ? AND EVENT_ID = ?";
        //     $stmt = $this->conn->prepare($sqlQuery);

        //     $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
        //     $stmt->bindParam(2, $this->id, PDO::PARAM_INT);

        //     $stmt->execute();
        //     $stmt_count = $stmt->rowCount();
        //     if($stmt_count>0){
        //         return true;
        //     }else{
        //         return false;
        //     }
        // }

        // This function checks if the user has already favourited an event
        public function checkEventFavourite($query_user_id){
            $sqlQuery = "SELECT EVENT_FAVOURITE_ID
                      FROM
                      event_favourite
                    WHERE 
                       USER_ID = ? AND EVENT_ID = ? AND EVENT_FAVOURITE = 'true'";
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

        // Function retrieves the events a user has voted on (REMOVED)
        // public function getUserEventVotes($query_user_id){
        //     $sqlQuery = "SELECT e.EVENT_ID,ev.EVENT_VOTE
        //                 FROM
        //                     event_vote as ev
        //                 INNER JOIN 
        //                     ". $this->db_table ." as e
        //                     ON ev.EVENT_ID = e.ID
        //                 WHERE 
        //                     USER_ID = ?";
        //     $stmt = $this->conn->prepare($sqlQuery);

        //     $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
        //     $stmt->execute();
        //     return $stmt;
        // }

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
                        event_favourite as ef
                    INNER JOIN event as e 
                       ON ef.EVENT_ID = e.ID
                    WHERE 
                       ef.USER_ID = ? AND ef.EVENT_FAVOURITE = 'true' ";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        }
         
        // unstars events
        //  public function Removefavourite($query_user_id){
        //  $sqlQuery = "DELETE 
        //             FROM
        //                 event_favourite as ef
        //             INNER JOIN event as e 
        //                ON ef.EVENT_ID = e.ID
        //             WHERE 
        //                ef.USER_ID = ? AND ef.EVENT_ID = ?  ";
        //     $stmt = $this->conn->prepare($sqlQuery);

        //     $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
        //     $stmt->bindParam(2, $this->id, PDO::PARAM_INT)
           

        //      if($stmt->execute()){
        //         return true;
        //     }
        //     return false;
        // }

        // Function fetches all the events with its votes
        public function fetchAll(){
            $sqlQuery = "SELECT e.EVENT_ID,
                            COUNT(IF(ev.EVENT_VOTE = 'true',ev.EVENT_VOTE_ID,NULL)) AS VOTES_TRUE,
                            COUNT(IF(ev.EVENT_VOTE = 'false',ev.EVENT_VOTE_ID,NULL)) AS VOTES_FALSE
                        FROM event_vote ev
                            INNER JOIN ". $this->db_table ." e
                            ON ev.EVENT_ID = e.ID
                        GROUP BY e.EVENT_ID";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();
            return $stmt;
        }

        // Funtion that fetches a count(no of stars) of each event
        public function fetchStarCount(){
            $sqlQuery = "SELECT e.EVENT_ID, COUNT(ef.EVENT_FAVOURITE) AS NO_STARS 
                        FROM event_favourite ef
                            INNER JOIN ". $this->db_table ." e 
                            ON ef.EVENT_ID = e.ID
                        WHERE ef.EVENT_FAVOURITE = 'true'
                        GROUP BY e.EVENT_ID";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        }


        // #################### UPDATE ####################

        // function that unstars/stars a users favourite event
        public function updateFavourite($query_user_id){
            $sqlQuery = "UPDATE event_favourite 
                        SET EVENT_FAVOURITE = 'false'
                        WHERE USER_ID = :user_id AND EVENT_ID = :event_id";
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
    }
?>
