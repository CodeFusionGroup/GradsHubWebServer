<?php
    class User{

        // Connection
        private $conn;

        // Table
        private $db_table = "user";

        // Columns
        public $id;
        public $f_name;
        public $l_name;
        public $password;
        public $email;
        public $phone_no;
        public $acad_status;
        public $fcm_token;

        // Db connection
        public function __construct(){

            // Get the database.php file
            require_once $_SERVER['DOCUMENT_ROOT'] .'/config/database.php';

            // Create a database object
            $database = new Database();
            $this->conn = $database->getConnection();

        }

        // #################### CREATE ####################

        // CREATE (Register)
        public function createUser(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        USER_FNAME = :f_name,
                        USER_LNAME = :l_name,
                        USER_PASSWORD = :password, 
                        USER_EMAIL = :email, 
                        USER_PHONE_NO = :phone_no, 
                        USER_ACAD_STATUS = :acad_status, 
                        USER_FCM_TOKEN = :fcm_token";
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->f_name=htmlspecialchars(strip_tags($this->f_name));
            $this->l_name=htmlspecialchars(strip_tags($this->l_name));
            $this->password=htmlspecialchars(strip_tags($this->password));
            $this->email=htmlspecialchars(strip_tags($this->email));
            $this->phone_no=htmlspecialchars(strip_tags($this->phone_no));
            $this->acad_status=htmlspecialchars(strip_tags($this->acad_status));
            $this->fcm_token=htmlspecialchars(strip_tags($this->fcm_token));

            // bind data
            $stmt->bindParam(":f_name", $this->f_name);
            $stmt->bindParam(":l_name", $this->l_name);
            $stmt->bindParam(":password", $this->password);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":phone_no", $this->phone_no);
            $stmt->bindParam(":acad_status", $this->acad_status);
            $stmt->bindParam(":fcm_token", $this->fcm_token);

            if($stmt->execute()){
               return true;
            }
            return false;
        }

        // #################### READ ####################


        // Get a user using an email
        public function getUserByEmail($query_email){
            $sqlQuery = "SELECT USER_ID,USER_FNAME,USER_LNAME,USER_PASSWORD,USER_PASSWORD
                            ,USER_EMAIL,USER_PHONE_NO, USER_ACAD_STATUS
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       USER_EMAIL = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_email, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        }

        //getting a specified token to send push to selected device
        public function getTokenByEmail($query_email){
            $sqlQuery = "SELECT USER_FCM_TOKEN
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       USER_EMAIL = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_email, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        }

        // GET ALL
        // public function getEmployees(){
        //     $sqlQuery = "SELECT * FROM " . $this->db_table . "";
        //     $stmt = $this->conn->prepare($sqlQuery);
        //     $stmt->execute();
        //     return $stmt;
        // }
        

        // READ single
        // public function getSingleEmployee(){
        //     $sqlQuery = "SELECT *
        //               FROM
        //                 ". $this->db_table ."
        //             WHERE 
        //                id = ?
        //             LIMIT 0,1";

        //     $stmt = $this->conn->prepare($sqlQuery);

        //     $stmt->bindParam(1, $this->id);

        //     $stmt->execute();

        //     $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
        //     $this->f_name = $dataRow['USER_FNAME'];
        //     $this->l_name = $dataRow['USER_LNAME'];
        //     $this->password = $dataRow['USER_PASSWORD'];
        //     $this->email = $dataRow['USER_EMAIL'];
        //     $this->phone_no = $dataRow['USER_PHONE_NO'];
        //     $this->acad_status = $dataRow['USER_ACAD_STATUS'];
        // }
        

        // UPDATE
        // public function updateEmployee(){
        //     $sqlQuery = "UPDATE
        //                 ". $this->db_table ."
        //             SET
        //                 name = :name, 
        //                 email = :email, 
        //                 age = :age, 
        //                 designation = :designation, 
        //                 created = :created
        //             WHERE 
        //                 id = :id";
        
        //     $stmt = $this->conn->prepare($sqlQuery);
        
        //     $this->name=htmlspecialchars(strip_tags($this->name));
        //     $this->email=htmlspecialchars(strip_tags($this->email));
        //     $this->age=htmlspecialchars(strip_tags($this->age));
        //     $this->designation=htmlspecialchars(strip_tags($this->designation));
        //     $this->created=htmlspecialchars(strip_tags($this->created));
        //     $this->id=htmlspecialchars(strip_tags($this->id));
        
        //     // bind data
        //     $stmt->bindParam(":name", $this->name);
        //     $stmt->bindParam(":email", $this->email);
        //     $stmt->bindParam(":age", $this->age);
        //     $stmt->bindParam(":designation", $this->designation);
        //     $stmt->bindParam(":created", $this->created);
        //     $stmt->bindParam(":id", $this->id);
        
        //     if($stmt->execute()){
        //        return true;
        //     }
        //     return false;
        // }

        // DELETE
        // function deleteEmployee(){
        //     $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE id = ?";
        //     $stmt = $this->conn->prepare($sqlQuery);
        
        //     $this->id=htmlspecialchars(strip_tags($this->id));
        
        //     $stmt->bindParam(1, $this->id);
        
        //     if($stmt->execute()){
        //         return true;
        //     }
        //     return false;
        // }

    }
?>