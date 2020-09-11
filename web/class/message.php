<?php

    class Message{
        // Connection
        private $conn;

        // Table
        private $db_table = "message";

        // Columns
        public $id;
        public $sender_id;
        public $chat_id;
        // public $recipient_id;
        public $timestamp;
        public $text;

        // Db connection
        public function __construct(){
            // Get the database.php file
            require_once $_SERVER['DOCUMENT_ROOT'] .'/config/database.php';

            // Create a database object
            $database = new Database();
            $this->conn = $database->getConnection();
        }

        // #################### CREATE ####################

        //Create a new message
        public function createMessage(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        SENDER_ID = :sender_id,
                        CHAT_ID = :chat_id,
                        MESSAGE_TIMESTAMP= :timestamp,
                        MESSAGE_TEXT = :text ";
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->sender_id=htmlspecialchars(strip_tags($this->sender_id));
            // $this->recipient_id=htmlspecialchars(strip_tags($this->recipient_id));
            $this->chat_id=htmlspecialchars(strip_tags($this->chat_id));
            $this->timestamp=htmlspecialchars(strip_tags($this->timestamp));
            $this->text=htmlspecialchars(strip_tags($this->text));

            // bind data
            $stmt->bindParam(":sender_id", $this->sender_id);
            // $stmt->bindParam(":recipient_id", $this->recipient_id);
            $stmt->bindParam(":chat_id", $this->chat_id);
            $stmt->bindParam(":timestamp", $this->timestamp);
            $stmt->bindParam(":text", $this->text);

            if($stmt->execute()){
               return true;
            }
            return false;
        }

        // #################### READ ####################

        // Find all messages in a specific chat
        // public function retrieveChatMessages($query_sender_id,$query_recipient_id){
        //     $sqlQuery = " SELECT m.MESSAGE_TIMESTAMP, m.MESSAGE_TEXT, CONCAT(u.USER_FNAME,' ',u.USER_LNAME) AS FULL_NAME 
        //             FROM
        //                 ". $this->db_table ." m
        //             INNER JOIN user u ON m.RECIPIENT_ID = u.USER_ID
        //             WHERE
        //                 ( m.SENDER_ID = ? OR m.RECIPIENT_ID = ? )
        //             AND
        //                 ( m.RECIPIENT_ID = ? OR m.SENDER_ID = ? )
        //             ORDER BY m.MESSAGE_TIMESTAMP ASC";
        //     $stmt = $this->conn->prepare($sqlQuery);

        //     $stmt->bindParam(1, $query_sender_id, PDO::PARAM_INT);
        //     $stmt->bindParam(2, $query_sender_id, PDO::PARAM_INT);

        //     $stmt->bindParam(3, $query_recipient_id, PDO::PARAM_INT);
        //     $stmt->bindParam(4, $query_recipient_id, PDO::PARAM_INT);

        //     $stmt->execute();
        //     return $stmt;
        // }

        // Find people to chat with (only people in common groups)
        public function retrieveCommonGroupUsers($query_user_id){
            $sqlQuery = " SELECT DISTINCT u.USER_ID,u.USER_FNAME,u.USER_LNAME,u.USER_EMAIL, u.USER_PHONE_NO, u.USER_ACAD_STATUS
                        FROM group_user gu
                            INNER JOIN research_group rg ON gu.GROUP_ID = rg.GROUP_ID
                            INNER JOIN user u ON gu.USER_ID = u.USER_ID 
                        WHERE rg.GROUP_NAME IN (
                            SELECT rg.GROUP_NAME 
                            FROM group_user gu
                                INNER JOIN research_group rg ON gu.GROUP_ID = rg.GROUP_ID
                                INNER JOIN user u ON gu.USER_ID = u.USER_ID 
                            WHERE u.USER_ID = ?)
                        AND u.USER_ID != ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $query_user_id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt;
        }

        // Find open chats
        // public function retrieveOpenChats($query_user_id){
        //     $sqlQuery = " SELECT DISTINCT u.USER_ID, u.USER_FNAME, u.USER_LNAME 
        //                 FROM user u
        //                     INNER JOIN message m ON u.USER_ID = m.TO_USER_ID
        //                 WHERE m.FROM_USER_ID = ?";
        //     $stmt = $this->conn->prepare($sqlQuery);

        //     $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);

        //     $stmt->execute();
        //     return $stmt;
        // }

    }
?>