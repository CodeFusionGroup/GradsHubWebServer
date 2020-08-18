<?php

    class GroupPost{
        // Connection
        private $conn;

        // Table
        private $db_table = "group_post";

        // Columns
        public $id;
        public $group_user_id;
        public $group_id;
        public $title;
        public $date;
        public $url;
        public $file;
        public $file_name;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }

        // #################### CREATE ####################

        // Create a new group post(URL)
        public function createPostUrl(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        GROUP_USER_ID = :group_user_id,
                        GROUP_ID = :group_id,
                        POST_TITLE = :title, 
                        POST_DATE = :date, 
                        POST_URL = :url";
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->group_user_id=htmlspecialchars(strip_tags($this->group_user_id));
            $this->group_id=htmlspecialchars(strip_tags($this->group_id));
            $this->title=htmlspecialchars(strip_tags($this->title));
            $this->date=htmlspecialchars(strip_tags($this->date));
            $this->url=htmlspecialchars(strip_tags($this->url));

            // bind data
            $stmt->bindParam(":group_user_id", $this->group_user_id);
            $stmt->bindParam(":group_id", $this->group_id);
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":date", $this->date);
            $stmt->bindParam(":url", $this->url);

            if($stmt->execute()){
                return true;
            }
            return false;
        }
        // Create a new group post(FILE)
        public function createPostFile(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        GROUP_USER_ID = :group_user_id,
                        GROUP_ID = :group_id,
                        POST_TITLE = :title, 
                        POST_DATE = :date, 
                        POST_FILE = :file,
                        POST_FILE_NAME = :file_name";
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->group_user_id=htmlspecialchars(strip_tags($this->group_user_id));
            $this->group_id=htmlspecialchars(strip_tags($this->group_id));
            $this->title=htmlspecialchars(strip_tags($this->title));
            $this->date=htmlspecialchars(strip_tags($this->date));
            $this->file=htmlspecialchars(strip_tags($this->file));
            $this->file_name=htmlspecialchars(strip_tags($this->file_name));

            // bind data
            $stmt->bindParam(":group_user_id", $this->group_user_id);
            $stmt->bindParam(":group_id", $this->group_id);
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":date", $this->date);
            $stmt->bindParam(":file", $this->file);
            $stmt->bindParam(":file_name", $this->file_name);

            if($stmt->execute()){
                return true;
            }
            return false;
        }

        // TODO: Function is not used anymore (possibly delete)
        public function uploadPDF(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        GROUP_USER_ID = :group_user_id,
                        GROUP_ID = :group_id,
                        POST_TITLE = :title, 
                        POST_DATE = :date, 
                        POST_FILE = :file";
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->group_user_id=htmlspecialchars(strip_tags($this->group_user_id));
            $this->group_id=htmlspecialchars(strip_tags($this->group_id));
            $this->title=htmlspecialchars(strip_tags($this->title));
            $this->date=htmlspecialchars(strip_tags($this->date));
            $this->file=htmlspecialchars(strip_tags($this->file));

            // bind data
            $stmt->bindParam(":group_user_id", $this->group_user_id);
            $stmt->bindParam(":group_id", $this->group_id);
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":date", $this->date);
            $stmt->bindParam(":attachment_file", $this->file);

            if($stmt->execute()){
                return true;
            }
            return false;
            
        }

        // #################### READ ####################

        // Find no. comments and likes in a group post
        public function readCommentsAndLikes(){
            $sqlQuery = " SELECT COALESCE( NO_OF_COMMENTS,0) AS NO_OF_COMMENTS,COALESCE( NO_OF_LIKES,0) AS NO_OF_LIKES
                    FROM ". $this->db_table ." AS gp
                    LEFT JOIN (
                    SELECT GROUP_POST_ID,COUNT(*) AS NO_OF_COMMENTS
                    FROM group_post_comment
                    GROUP BY GROUP_POST_ID 
                    ) NO_OF_COMMENTS ON NO_OF_COMMENTS.GROUP_POST_ID = gp.GROUP_POST_ID
                    LEFT JOIN (
                    SELECT GROUP_POST_ID,COUNT(*) AS NO_OF_LIKES
                    FROM group_post_like
                    GROUP BY GROUP_POST_ID 
                    ) NO_OF_LIKES ON NO_OF_LIKES.GROUP_POST_ID = gp.GROUP_POST_ID
                    WHERE gp.GROUP_ID = ?
                    ORDER BY gp.POST_DATE DESC, gp.GROUP_POST_ID";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $this->group_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        }

        // Select all posts in a group
        public function read(){
            $sqlQuery = " SELECT u.USER_FNAME, u.USER_LNAME, gp.GROUP_POST_ID,gp.POST_TITLE
                    , gp.POST_DATE,gp.POST_URL,gp.POST_FILE,gp.POST_FILE_NAME
                    FROM ". $this->db_table ." as gp 
                    INNER JOIN group_user as gu ON gp.GROUP_USER_ID = gu.GROUP_USER_ID
                    INNER JOIN user as u ON gu.USER_ID = u.USER_ID
                    where gp.GROUP_ID = ?
                    ORDER BY gp.POST_DATE DESC, gp.GROUP_POST_ID";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $this->group_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        }

        // TODO: Function is not used (possibly delete)
        // retrieves url for downloading a pdf file
        public function downloadFile($query_post_id){
            // $sqlQuery = "SELECT gp.GROUP_POST_ID FROM
            //             ". $this->db_table ." AS gp
            //             INNER JOIN group_user AS gu ON gp.GROUP_USER_ID = gu.GROUP_USER_ID

            //             WHERE 

            //             gu.USER_ID = ? AND gu.GROUP_ID = ?";
            $sqlQuery = "SELECT POST_FILE FROM
                        ". $this->db_table ."
                        WHERE 
                        GROUP_POST_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_post_id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt;
        }



    }

?>
