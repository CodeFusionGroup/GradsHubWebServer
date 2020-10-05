<?php

    //Import PHPMailer classes into the global namespace
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

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
        public $profile_picture;

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

        public function phpMailer($query_email,$key,$query_name){

            // Include the composer generated autoload.php file
            require('../../../vendor/autoload.php');
            //importing the variables files
            require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
            

            //create the PHPMailer class
            $mail = new PHPMailer();

            //SMTP configuration and Server settings
            $mail->isSMTP();
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAuth = true;
            $mail->Username = EMAIL_ADDRESS;
            $mail->Password = EMAIL_PASSWORD;

            //Recipients
            $mail->setFrom('no-reply@gradshub.com', 'Gradshub Support');
            $mail->addAddress($query_email, $query_name);
            $mail->Subject = 'GradsHub: Password Recovery';
            

            // https://gradshub.herokuapp.com
            // http://localhost:8080
            // Content
            $mail->isHTML(true);
            $content = '
            <html>
            <head>
            <title>GradsHub: Password Recovery</title>
            </head>
            <body>
            <p>Dear ' .$query_name. ',</p>
            <p>To change your password please click the link below:</p>
            <p>-------------------------------------------------------------</p>
            <p> 
                <a href = "https://gradshub.herokuapp.com/api/User/password-recovery.php?key='. $key . '&email='.$query_email.'&action=reset" target="_blank"> 
                https://gradshub.herokuapp.com/api/User/password-recovery.php?key='. $key . '&email='.$query_email.'&action=reset
                </a> 
            </p>
            <p>-------------------------------------------------------------</p>
            <p>Please note that for security reasons the link will expire in one day(24 hours).</p>
            <p>If you did not request this password recovery link, no action 
            is needed, your password will not be reset. However, you may want to log into 
            your account and change your password as a precaution.</p>
            <p>Kind regards</p>
            <p>Gradshub Team</p>
            </body>
            </html>';
            $mail->Body = $content;

            if ($mail->send()) {
                return true;
                // Save message to 'Sent Mail' folder
                save_mail($mail);
            }
            echo 'Mailer Error: '. $mail->ErrorInfo;
            return false;

        }

        function save_mail($mail){

            // Path to save email to a specific tag/folder
            $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';

            // )pen an IMAP connection using the same username and password used for SMTP
            $imapStream = imap_open($path, $mail->Username, $mail->Password);

            $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
            imap_close($imapStream);

            return $result;
        }

        // Create a recovery record
        public function insertRecovery($query_email,$query_key,$query_date){
            $sqlQuery = "INSERT INTO
                        password_recovery
                    SET
                        RECOVERY_EMAIL = :user_email,
                        RECOVERY_KEY = :recovery_key,
                        RECOVERY_EXP_DATE = :exp_date";
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $query_email=htmlspecialchars(strip_tags($query_email));
            $query_key=htmlspecialchars(strip_tags($query_key));
            $query_date=htmlspecialchars(strip_tags($query_date));


            // bind data
            $stmt->bindParam(":user_email", $query_email);
            $stmt->bindParam(":recovery_key", $query_key);
            $stmt->bindParam(":exp_date", $query_date);

            if($stmt->execute()){
               return true;
            }
            return false;
        }

        // #################### READ ####################

        // Get a user using an email
        public function getUserByEmail($query_email){
            $sqlQuery = "SELECT USER_ID,USER_FNAME,USER_LNAME,USER_PASSWORD,USER_PASSWORD
                            ,USER_EMAIL,USER_PHONE_NO, USER_ACAD_STATUS, USER_PROFILE_PICTURE
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       USER_EMAIL = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_email, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        }

        // Check if user exists using email
        public function checkExists($query_email){
            $sqlQuery = "SELECT USER_ID
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       USER_EMAIL = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_email, PDO::PARAM_STR);
            $stmt->execute();

            $stmt_count = $stmt->rowCount();
            if($stmt_count>0){
                return true;
            }
            return false;
        }

        // Check if emails are the same
        public function emailsEqual($query_user_id,$query_email){
            $sqlQuery = "SELECT USER_EMAIL
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       USER_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();
            
            // Check if emails match
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $email = $row['USER_EMAIL'];

            if($email == $query_email){
                return true;
            }
            return false;
        }

        //getting a specified token to send push to selected device
        public function getTokenByID($query_user_id){
            $sqlQuery = "SELECT USER_FCM_TOKEN
                      FROM
                        ". $this->db_table ."
                    WHERE 
                        USER_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();

            // Get the token
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            $token = $dataRow['USER_FCM_TOKEN'];
            $token_arr = array(); //TODO: Probably change to one token
            array_push($token_arr,$token);

            return $token_arr;
        }

        // Get the full name of a user
        public function getFullName($query_user_id){
            $sqlQuery = "SELECT USER_FNAME,USER_LNAME
                      FROM
                        ". $this->db_table ."
                    WHERE 
                        USER_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt;
        }
        
         // Get user profile infomation 
        public function getProfile($query_user_id){
            $sqlQuery = "SELECT USER_ID,USER_LNAME,USER_FNAME
                            ,USER_EMAIL,USER_PHONE_NO, USER_ACAD_STATUS, USER_PROFILE_PICTURE
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       USER_ID = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt;
        }

        // Check if password recovery exists
        public function recoveryExist($query_user_email,$query_key){
            $sqlQuery = "SELECT RECOVERY_EMAIL,RECOVERY_KEY
                            ,RECOVERY_EXP_DATE
                      FROM
                        password_recovery
                    WHERE 
                    RECOVERY_EMAIL = ? AND RECOVERY_KEY = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $query_user_email, PDO::PARAM_STR);
            $stmt->bindParam(2, $query_key, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        }


        // #################### UPDATE ####################

        // UPDATE the fcm token
        public function updateFCMToken(){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        USER_FCM_TOKEN = :fcm_token
                    WHERE 
                        USER_ID = :user_id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // Sanitise the data
            $this->id=htmlspecialchars(strip_tags($this->id));
            $this->fcm_token=htmlspecialchars(strip_tags($this->fcm_token));
            
            // bind data
            $stmt->bindParam(":user_id", $this->id);
            $stmt->bindParam(":fcm_token", $this->fcm_token);
            
            if($stmt->execute()){
               return true;
            }
            return false;
        }

        // Change/update the user's password
        public function updatePassword($query_user_id,$query_new_pass){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        USER_PASSWORD = :user_pass
                    WHERE 
                        USER_ID = :user_id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            $query_user_id=htmlspecialchars(strip_tags($query_user_id));
            $query_new_pass=htmlspecialchars(strip_tags($query_new_pass));
        
            // bind data
            $stmt->bindParam(":user_id", $query_user_id);
            $stmt->bindParam(":user_pass", $query_new_pass);
        
            if($stmt->execute()){
               return true;
            }
            return false;
        }
        
        // Update the user's profile
        public function updateProfile(){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        USER_FNAME = :fname,
                        USER_LNAME = :lname,
                        USER_EMAIL = :email,
                        USER_PHONE_NO = :phone_no, 
                        USER_ACAD_STATUS = :academic_status
                    WHERE
                        USER_ID= :user_id";
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->id=htmlspecialchars(strip_tags($this->id));
            $this->fname=htmlspecialchars(strip_tags($this->fname));
            $this->lname=htmlspecialchars(strip_tags($this->lname));
            $this->email=htmlspecialchars(strip_tags($this->email));
            $this->phone_no=htmlspecialchars(strip_tags($this->phone_no));
            $this->academic_status=htmlspecialchars(strip_tags($this->acad_status));
            

            // bind data
            $stmt->bindParam(":user_id", $this->id);
            $stmt->bindParam(":fname", $this->fname);
            $stmt->bindParam(":lname", $this->lname);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":phone_no", $this->phone_no);
            $stmt->bindParam(":academic_status", $this->acad_status);

            if($stmt->execute()){
               return true;
            }
            return false;
        }

        public function updateProfilePic($user_id,$profile_pic){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        USER_PROFILE_PICTURE = :profile_pic 
                    WHERE
                        USER_ID= :user_id";
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $user_id=htmlspecialchars(strip_tags($user_id));
            $profile_pic=htmlspecialchars(strip_tags($profile_pic));

            // bind data
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":profile_pic", $profile_pic);

            if($stmt->execute()){
                return true;
            }
            return false;
        }

        // #################### DELETE ####################

        // DELETE password recovery record
        // TODO: Instead of deleting record add a column to indicate whether
        // the password has been changed or not
        function deleteRecovery($recovery_email){
            $sqlQuery = "DELETE FROM password_recovery WHERE RECOVERY_EMAIL = ?";
            $stmt = $this->conn->prepare($sqlQuery);
        
            $recovery_email=htmlspecialchars(strip_tags($recovery_email));
        
            $stmt->bindParam(1, $recovery_email);
        
            if($stmt->execute()){
                return true;
            }
            return false;
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
