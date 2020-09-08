<?php

    class GroupPostComment{
        // Connection
        private $conn;

        // Table
        private $db_table = "group_post_comment";

        // Columns
        public $id;
        public $group_post_id;
        public $group_user_id;
        public $comment;
        public $comment_date;

        // Db connection
        public function __construct(){
            // Get the database.php file
            require_once $_SERVER['DOCUMENT_ROOT'] .'/config/database.php';

            // Create a database object
            $database = new Database();
            $this->conn = $database->getConnection();
        }

        // #################### CREATE ####################

        public function create(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        GROUP_POST_ID = :group_post_id,
                        GROUP_USER_ID = :group_user_id,
                        POST_COMMENT = :comment, 
                        POST_COMMENT_DATE = :comment_date";
             $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->group_post_id=htmlspecialchars(strip_tags($this->group_post_id));
            $this->group_user_id=htmlspecialchars(strip_tags($this->group_user_id));
            $this->comment=htmlspecialchars(strip_tags($this->comment));
            $this->comment_date=htmlspecialchars(strip_tags($this->comment_date));

            // bind data
            $stmt->bindParam(":group_post_id", $this->group_post_id);
            $stmt->bindParam(":group_user_id", $this->group_user_id);
            $stmt->bindParam(":comment", $this->comment);
            $stmt->bindParam(":comment_date", $this->comment_date);

            if($stmt->execute()){
                return true;
            }
            return false;
        }


        // #################### READ ####################

        public function readAll(){
            $sqlQuery = "SELECT u.USER_FNAME,u.USER_LNAME,gpc.POST_COMMENT,gpc.POST_COMMENT_DATE
                        FROM ". $this->db_table ." AS gpc
                        INNER JOIN group_user AS gu ON gpc.GROUP_USER_ID = gu.GROUP_USER_ID
                        INNER JOIN user AS u ON gu.USER_ID = u.USER_ID
                    WHERE 
                        gpc.GROUP_POST_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $this->group_post_id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt;
        }
    }


?>