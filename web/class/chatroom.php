<?php

    class Chatroom{
        // Connection
        private $conn;

        // Table
        private $db_table = "chatroom";

        // Columns
        //public $id;
        public $chatroom_id;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }

        // #################### CREATE ####################

        // CREATE
        public function createChatroom(){

            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        CHATROOM_ID = : chatroom_id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->chatroom_id=htmlspecialchars(strip_tags($this->chatroom_id));

            // bind data
            $stmt->bindParam(":chatroom_id", $this->chatroom_id);

            if($stmt->execute()){
               return true;
            }
            return false;

        }
        

        //saves messages to database
        /*public function saveChat(){
            $sqlQuery= "INSERT INTO 
                    messages
                    SET
                        USER_ID = :user_id,
                        MESSAGE_TEXT= :messages";

            $stmt = $this->conn->prepare($sqlQuery);

              // sanitize
              $this->user_id=htmlspecialchars(strip_tags($this->user_id));
              $this->messages=htmlspecialchars(strip_tags($this->messages));


              // bind data
              $stmt->bindParam(":user_id", $this->user_id);
              $stmt->bindParam(":messages", $this->messsages);
  
              if($stmt->execute()){
                 return true;
              }
              return false;
  
        }*/

       //
       public function getChatroom(){
        $sqlQuery= "SELECT m.MESSAGE_ID,
                           m.MESSAGE_TEXT,
                           u.USER_ID
  
                    FROM messages AS m
                    INNER JOIN user AS u ON m.USER_ID = U.USER_ID
                    WHERE m.CHATROOM_ID=2;

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
            return $stmt;

       }
       

    }
  
?>
