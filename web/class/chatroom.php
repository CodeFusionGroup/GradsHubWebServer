<?php

    class Chatroom{
        // Connection
        private $conn;

        // Table
        private $db_table = "chatroom";

        // Columns
        //public $id;
        public $chat_id;
        public $messages;
        public $dates;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }

        // #################### CREATE ####################

        // CREATE
        public function createChat(){

            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        CHAT_ID = : chat_id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->chat_id=htmlspecialchars(strip_tags($this->chat_id));

            // bind data
            $stmt->bindParam(":chat_id", $this->chat_id);

            if($stmt->execute()){
               return true;
            }
            return false;

        }
        

        //saves messages to database
        public function saveChat(){
            $sqlQuery= "INSERT INTO 
                    ". $this->db_table ."
                    SET
                        USER_ID = :user_id,
                        MESSAGES = :messages";

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
  
        }

       //
       public function getChatroom(){
        $sqlQuery= "SELECT C.* 
        FROM ". $this->db_table ." AS c
         INNER JOIN 
             user AS u ON c.USER_ID=u.USER_ID";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
            return $stmt;

       }
       

    }
  
?>