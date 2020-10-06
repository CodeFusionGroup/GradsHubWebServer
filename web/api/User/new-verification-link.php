<?php

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the classes
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/user.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/email.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/log.php';

    // Create User object
    $user_obj = new User();

    // Create Log object
    $log_obj = new Log();

    // Create Log object
    $email_obj = new Email();

    // It a request for the form
    if(isset($_GET['action']) && ($_GET['action']=='new-link-form') && !isset($_POST["action"])){
        //Form
        readfile("../../templates/requestForm.html");

        // Log Verification from
        $log_msg = "{New Verification Link} New Request Form";
        $log_obj->infoLog($log_msg);
    }

    // After submitting the form
    if( isset($_POST['email'],$_POST["action"]) && !empty($_POST['email']) && ($_POST["action"]=="new link")){
        //Form submitted
        
        //Form data
        $email = $_POST["email"];

        // Verification date/timestamp
        $date_format = mktime( date("H"), date("i"), date("s"),
                    date("m") ,date("d")+1, date("Y") );
        $verify_date = date("Y-m-d H:i:s",$date_format);
        // The verification code
        $verify_code = md5(rand(0,1000));

        // Check if user exists
        if($user_obj->checkExists($email)){

            // Check if user is already verified
            if($user_obj->checkVerified($email)){
                //User already verified leave!
                readfile("../../templates/verifiedValid.html");

                // Log Verification attempt
                $log_msg = "{New Verification Link} Already verified user: ". $email .", tried to verify again.";
                $log_obj->infoLog($log_msg);

            }else{
                //User not verified continue

                // Get the fullname
                $stmnt_user = $user_obj->getUserByEmail($email);
                $user = $stmnt_user->fetch(PDO::FETCH_ASSOC);
                $fullname = $user['USER_FNAME'] ." ". $user["USER_LNAME"];

                // Verify details in the database
                if( $user_obj->updateVerifyDetails($email, $verify_code, $verify_date ) ){
                    //Details updated

                    // Send an email to the user
                    if( $email_obj->userVerification($email, $verify_code, $fullname) ){

                        readfile("../../templates/emailSent.html");

                        // Log New Verification email sent
                        $log_msg = "{New Verification Link} New verification email sent to ". $email;
                        $log_obj->infoLog($log_msg);

                    }else{
                        // Could not send email
                        readfile("../../templates/emailNotSent.html");

                        // Log error sending email
                        $log_msg = "{New Verification Link} Verification email to ". $email .", could not be sent.";
                        $log_obj->errorLog($log_msg);
                    }

                }else{
                    // Details could not be updated in db

                    // Log error to update verify details
                    $log_msg = "{New Verification Link} Verify details for ". $email .", could not be updated in DB";
                    $log_obj->infoLog($log_msg);
                }

            }

        }else{
            // User/email does not exist
            readfile("../../templates/emailInvalid.html");

            // Log user/email does not exist
            $log_msg = "{New Verification Link} Email:". $email ."tried to request a new link with an email that doesnt exist.";
            $log_obj->infoLog($log_msg);
        }
        
    }

?>