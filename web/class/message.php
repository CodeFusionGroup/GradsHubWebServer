<?php

    class Message{
        // Connection
        private $conn;

        // Table
        private $db_table = "message";

        // Columns
        public $id;
        public $sender_id;
        public $recipient_id;
        public $date;
        public $text;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }

        // #################### CREATE ####################

        //Create a new message
        public function createMessage(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        FROM_USER_ID = :sender_id,
                        TO_USER_ID = :recipient_id,
                        DATE_SENT= :date,
                        MESSAGE_TEXT = :text";
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->sender_id=htmlspecialchars(strip_tags($this->sender_id));
            $this->recipient_id=htmlspecialchars(strip_tags($this->recipient_id));
            $this->date=htmlspecialchars(strip_tags($this->date));
            $this->text=htmlspecialchars(strip_tags($this->text));

            // bind data
            $stmt->bindParam(":sender_id", $this->sender_id);
            $stmt->bindParam(":recipient_id", $this->recipient_id);
            $stmt->bindParam(":date", $this->date);
            $stmt->bindParam(":text", $this->text);

            if($stmt->execute()){
               return true;
            }
            return false;
        }

        // #################### READ ####################

        // Find all messages in a specific chat
        public function retrieveChatMessages($query_sender_id,$query_recipient_id){
            $sqlQuery = " SELECT DATE_SENT,MESSAGE_TEXT 
                        FROM
                        ". $this->db_table ."
                    WHERE
                        FROM_USER_ID = ?,
                        TO_USER_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_sender_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $query_recipient_id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt;
        }

        // Find people to chat with (only people in common groups)
        public function retrieveCommonGroupUsers($query_user_id){
            $sqlQuery = " SELECT DISTINCT u.USER_ID,u.USER_FNAME,u.USER_LNAME 
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
        public function retrieveOpenChats($query_user_id){
            $sqlQuery = " SELECT u.USER_ID, u.USER_FNAME, u.USER_LNAME 
                        FROM user u
                            INNER JOIN message m ON u.USER_ID = m.TO_USER_ID
                        WHERE m.FROM_USER_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt;
        }

    }
?>