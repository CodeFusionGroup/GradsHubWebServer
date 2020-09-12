<?php

    class Chat{
        // Connection
        private $conn;

        // Table
        private $db_table = "chat";

        // Columns
        public $id;
        public $name;

        // Db connection
        public function __construct(){
            // Get the database.php file
            require_once $_SERVER['DOCUMENT_ROOT'] .'/config/database.php';

            // Create a database object
            $database = new Database();
            $this->conn = $database->getConnection();
        }

        // #################### CREATE ####################

        // CREATE a new chat
        public function createChat(){

            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        CHAT_NAME = :name";
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->name=htmlspecialchars(strip_tags($this->name));

            // bind data
            $stmt->bindParam(":name", $this->name);

            if($stmt->execute()){
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        }

        // Create a new participant in a chat
        public function createChatParticipant($query_user_id){

            $sqlQuery = "INSERT INTO
                        chat_participant
                    SET
                        PARTICIPANT_ID = :user_id,
                        CHAT_ID = :chat_id";
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $query_user_id=htmlspecialchars(strip_tags($query_user_id));
            $this->id=htmlspecialchars(strip_tags($this->id));

            // bind data
            $stmt->bindParam(":user_id", $query_user_id);
            $stmt->bindParam(":chat_id", $this->id);

            if($stmt->execute()){
               return true;
            }
            return false;
        }

        
        // #################### READ ####################

        // Get All Open chats for a user
        public function getOpenChats($query_user_id){
            $sqlQuery = "SELECT c.CHAT_ID 
                    FROM ". $this->db_table ." c 
                        INNER JOIN chat_participant cp 
                        ON c.CHAT_ID = cp.CHAT_ID
                    WHERE cp.PARTICIPANT_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt;
        }

        // Get the most recent message in a chat
        public function getRecentMessage($query_chat_id){
            $sqlQuery = "SELECT u.USER_ID ,CONCAT(u.USER_FNAME,' ',u.USER_LNAME) AS FULL_NAME, m.MESSAGE_TEXT, m.MESSAGE_TIMESTAMP 
                    FROM message m
                        INNER JOIN user u ON m.SENDER_ID = u.USER_ID
                        WHERE m.CHAT_ID = ?
                    ORDER BY m.MESSAGE_TIMESTAMP DESC
                    LIMIT 1";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_chat_id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt;
        }

        //Check if a chat already exists
        public function chatExist($query_chat_name1,$query_chat_name2){
            $sqlQuery = "SELECT CHAT_ID,CHAT_NAME
                    FROM ". $this->db_table ."
                        WHERE CHAT_NAME = ? OR CHAT_NAME = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_chat_name1, PDO::PARAM_STR);
            $stmt->bindParam(2, $query_chat_name2, PDO::PARAM_STR);

            $stmt->execute();
            $stmt_count = $stmt->rowCount();

            if( $stmt_count > 0 ){
                // Chat exists
                $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->id = $dataRow['CHAT_ID'];
                return true;
            }
            return false;
        }

        // Get all messages in a chat
        public function getMessages(){
            $sqlQuery = "SELECT m.MESSAGE_TIMESTAMP, m.MESSAGE_TEXT, concat( u.USER_FNAME,' ',u.USER_LNAME) AS SENT_BY 
                    FROM message m
                        INNER JOIN user u ON m.SENDER_ID = u.USER_ID
                    WHERE CHAT_ID = ?
                    ORDER BY MESSAGE_TIMESTAMP";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $this->id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt;
        }

    //    public function getChatroom(){
    //     $sqlQuery= "SELECT m.MESSAGE_ID,
    //                        m.MESSAGE_TEXT,
    //                        u.USER_ID
  
    //                 FROM messages AS m
    //                 INNER JOIN user AS u ON m.USER_ID = U.USER_ID
    //                 WHERE m.CHATROOM_ID=2;

    //     $stmt = $this->conn->prepare($sqlQuery);

    //     $stmt->execute();
    //         return $stmt;

    //    }
       

    }
  
?>