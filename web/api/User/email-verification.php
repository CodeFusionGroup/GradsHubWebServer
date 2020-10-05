<?php

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';

    // Get the User class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/user.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/log.php';

    // Create User object
    $user_obj = new User();

    // Create Log object
    $log_obj = new Log();

    // Check if data is sent
    if( isset($_GET['code'],$_GET["action"],$_GET["email"]) && ($_GET["action"]=="verify") ){

        //Form data
        $code = $_GET['code'];
        $email = $_GET["email"];
        $cur_date = date("Y-m-d H:i:s");

        // Check if verifcation exists for user
        $stmnt = $user_obj->verifyExist($email, $code);
        $stmnt_count = $stmnt->rowCount();

        if($stmnt_count >0){

            // Check the expiry
            $stmnt_row = $stmnt->fetch(PDO::FETCH_ASSOC);
            $exp_date = $stmnt_row['USER_VERIFY_DATE'];

            if($exp_date >= $cur_date){

                // Activation Successful
                if($user_obj->verifyUser($email)){

                    // Read verified email template
                    readfile("../../templates/verificationSuccess.html");

                    // Log verification Success 
                    $log_msg = "{Account Verification}  User: ". $email . " successfully verified account";
                    $log_obj->infoLog($log_msg);

                }else{

                    // Could not verify user

                    // Log Error 
                    $log_msg = "{Account Verification}  User: ". $email . " could not be verified in db.";
                    $log_obj->errorLog($log_msg);
                }
            }else{
                // If link has expired
                readfile("../../templates/linkExpired.html");
                // Log expired link
                $log_msg = "{Account Verification}  User: ". $email . " has used an expired link/key, ". $code;
                $log_obj->infoLog($log_msg);
            }

        }else{
            // If link is invalid
            readfile("../../templates/linkInvalid.html");
            // Log Invalid link
            $log_msg = "{Account Verification} User: ". $email . " has used an invalid link/key, " . $code;
            $log_obj->infoLog($log_msg);
        }

    }

?>