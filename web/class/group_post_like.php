<?php

    class GroupPostLike{
        // Connection
        private $conn;

        // Table
        private $db_table = "group_post_like";

        // Columns
        public $id;
        public $group_post_id;
        public $group_user_id;
        public $post_like;

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
                        POST_LIKE = :post_like";
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->group_post_id=htmlspecialchars(strip_tags($this->group_post_id));
            $this->group_user_id=htmlspecialchars(strip_tags($this->group_user_id));
            $this->post_like=htmlspecialchars(strip_tags($this->post_like));

            // bind data
            $stmt->bindParam(":group_post_id", $this->group_post_id, PDO::PARAM_INT);
            $stmt->bindParam(":group_user_id", $this->group_user_id, PDO::PARAM_INT);
            $stmt->bindParam(":post_like", $this->post_like, PDO::PARAM_STR );

            if($stmt->execute()){
                return true;
            }
            return false;
        }


        // #################### READ ####################

        public function readUserLikes($query_user_id,$query_group_id){
            $sqlQuery = "SELECT gpl.GROUP_POST_ID
                    FROM 
                        ". $this->db_table ." AS gpl
                        INNER JOIN group_user AS gu ON gpl.GROUP_USER_ID = gu.GROUP_USER_ID
                    WHERE 
                    gu.USER_ID = ? AND gu.GROUP_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $query_group_id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt;
        }

        public function checkPostLiked($query_post_id,$query_group_user_id){
            $sqlQuery = "SELECT POST_LIKE_ID
                      FROM
                      ". $this->db_table ."
                    WHERE 
                       GROUP_USER_ID = ? AND GROUP_POST_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_group_user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $query_post_id, PDO::PARAM_INT);

            $stmt->execute();
            $stmt_count = $stmt->rowCount();
            if($stmt_count>0){
                return true;
            }else{
                return false;
            }

        }

        // Get the number of likes for a post
        public function getNoOfLikes($post_id){
            $sqlQuery = "SELECT COUNT(*) AS NO_OF_LIKES 
                    FROM ". $this->db_table ."
                    WHERE GROUP_POST_ID =?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $post_id, PDO::PARAM_INT);

            // Return result(count)
            $stmt->execute();
            $count = $stmt->fetch(PDO::FETCH_ASSOC);
            return $count;
        }
    }


?>