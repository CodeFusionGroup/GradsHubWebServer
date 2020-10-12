<?php

    class Group{

        // Connection
        private $conn;

        // Table
        private $db_table = "research_group";

        // Columns
        public $id;
        public $name;
        public $visibility;
        public $code;

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
        public function createGroup(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        GROUP_NAME = :name,
                        GROUP_VISIBILITY = :visibility,
                        GROUP_CODE = :code";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->name=htmlspecialchars(strip_tags($this->name));
            $this->visibility=htmlspecialchars(strip_tags($this->visibility));
            $this->code=htmlspecialchars(strip_tags($this->code));

            // bind data
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":visibility", $this->visibility);
            $stmt->bindParam(":code", $this->code);

            if($stmt->execute()){
               return true;
            }
            return false;
        }

        // Create a group admin
        public function createGroupAdmin($query_user_id){
            $sqlQuery = "INSERT INTO group_admin 
                    SET
                        USER_ID = :user_id,
                        GROUP_ID = :group_id
                        ";
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $user_id=htmlspecialchars(strip_tags($query_user_id));
            $group_id=htmlspecialchars(strip_tags($this->id));

            // bind data
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->bindParam(":group_id", $group_id,PDO::PARAM_INT);


            if($stmt->execute()){
                return true;
             }
             return false;

        }

        // Create a group member
        public function createGroupMember($query_user_id){
            $sqlQuery = "INSERT INTO group_user 
                    SET 
                        USER_ID = :user_id,
                        GROUP_ID = :group_id
                        ";
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $user_id=htmlspecialchars(strip_tags($query_user_id));
            $group_id=htmlspecialchars(strip_tags($this->id));

            // bind data
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->bindParam(":group_id", $group_id, PDO::PARAM_INT);

            if($stmt->execute()){
                return true;
             }
             return false;

        }

        // #################### READ ####################

        // Get a group using the group name
        public function getGroupByName($query_name){
            $sqlQuery = "SELECT *
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       GROUP_NAME = ?";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_name, PDO::PARAM_STR);

            $stmt->execute();
            return $stmt;

        }

        // Check if user is already a member of the group
        public function checkGroupMember($query_user_id,$query_group_id){
            $sqlQuery = "SELECT *
                      FROM
                        group_user
                    WHERE 
                       USER_ID = ? AND GROUP_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $query_group_id, PDO::PARAM_INT);

            $stmt->execute();
            $stmt_count = $stmt->rowCount();
            if($stmt_count>0){
                return true;
            }else{
                return false;
            }  
        }

        // Retrieve group member
        public function getGroupMember($query_user_id,$query_group_id){
            $sqlQuery = "SELECT *
                      FROM
                        group_user
                    WHERE 
                       USER_ID = ? AND GROUP_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $query_group_id, PDO::PARAM_INT);

            $stmt->execute();
            $stmt_count = $stmt->rowCount();
            if($stmt_count>0){
                $data_row = $stmt->fetch(PDO::FETCH_ASSOC); 
                return $data_row;
            }else{
                return false;
            }  
        }

        // Check if group code is correct
        public function checkCode($query_group_id,$query_code){
            $sqlQuery = "SELECT GROUP_ID,GROUP_CODE
                        FROM
                        ". $this->db_table ."
                    WHERE 
                        GROUP_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_group_id, PDO::PARAM_INT);
            $stmt->execute();

            $data_row = $stmt->fetch(PDO::FETCH_ASSOC);

            if($query_code == $data_row["GROUP_CODE"]){
                return true;
            }else{
                return false;
            }

        }

        // Retrieve the groups a user has joined (with the group admin(s))
        public function getUserGroups($query_user_id){
            $sqlQuery = "SELECT rg.GROUP_ID, rg.GROUP_NAME,rg.GROUP_VISIBILITY, rg.GROUP_CODE, u.USER_EMAIL AS GROUP_ADMIN
                    FROM group_user gu
                    INNER JOIN ". $this->db_table ." rg ON gu.GROUP_ID = rg.GROUP_ID
                    INNER JOIN group_admin ga ON rg.GROUP_ID = ga.GROUP_ID
                    INNER JOIN user u ON ga.USER_ID = u.USER_ID
                    WHERE gu.USER_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        }

        // Retrieve the groups a user has NOT joined
        public function getAvailableGroups($query_user_id){
            $sqlQuery = "SELECT rg.GROUP_ID,rg.GROUP_NAME, rg.GROUP_VISIBILITY 
                    FROM ". $this->db_table ." rg
                    WHERE rg.GROUP_ID 
                    NOT IN(
                    SELECT gu.GROUP_ID FROM group_user gu 
                    INNER JOIN user u ON gu.USER_ID = u.USER_ID
                    WHERE gu.USER_ID = ? )";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;

        }

        // Retrieve the groups a user is a member of
        public function getGroups($query_user_id){
            $sqlQuery = "SELECT rg.GROUP_ID FROM research_group rg
                    INNER JOIN group_user gu ON rg.GROUP_ID = gu.GROUP_ID
                    INNER JOIN user u ON gu.USER_ID = u.USER_ID
                    WHERE u.USER_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        }
    }

?>