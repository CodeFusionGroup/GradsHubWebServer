<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the classes
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/user.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/log.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/email.php';

    // Create User object
    $user_obj = new User();
    // Create Log object
    $log_obj = new Log();
    // Create Email object
    $email_obj = new Email();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));
   
    //Mocking database for testing purpose
    if($data->email == "tester141414fhfvbd@gmail.com"){
    	//Test registration success
		$output["success"]="-1";
		$output["message"]="Registration test successful!";
		echo json_encode($output);
    }

    // Make sure data is not empty
    else if( isset($data->f_name,$data->l_name,$data->password,
        $data->email,$data->phone_no,$data->acad_status) ){
            //,$data->fcm_token

            // Ensure User does not already exist
            $user_query = $user_obj->getUserByEmail($data->email);
            $count_user_query = $user_query->rowCount();
            if($count_user_query == 0 ){

                // Check that phone number is 10 digits
                if(strlen($data->phone_no) == 10){

                    // Set the user property values
                    $user_obj->f_name = $data->f_name;
                    $user_obj->l_name = $data->l_name;
                    //Hash the password
                    $hashed_password = password_hash($data->password,PASSWORD_DEFAULT);
                    $user_obj->password = $hashed_password;
                    $user_obj->email = $data->email;
                    $user_obj->phone_no = $data->phone_no;
                    $user_obj->acad_status = $data->acad_status;
                    $user_obj->fcm_token = $data->fcm_token;

                    // The verification date
                    $date_format = mktime( date("H"), date("i"), date("s"),
                    date("m") ,date("d")+1, date("Y") );
                    $user_obj->verify_date = date("Y-m-d H:i:s",$date_format);
                    // The verification code
                    $user_obj->verify_code = md5(rand(0,1000)); // md5($data->email.time()

                    //Fullname
                    $fullname = $data->f_name." ". $data->l_name;

                    // Create the user
                    if( $user_obj->createUser() ){

                        // Send an email to user
                        if($email_obj->userVerification($data->email,$user_obj->verify_code, $fullname)){
                            
                            //Email sent
                            $output["success"]="1";
                            $output["message"]="Please check email to verify account";
                            echo json_encode($output);

                            // Log Verification email sent
                            $log_msg = "Verification email sent to ". $data->email;
                            $log_obj->infoLog($log_msg);

                        }else{
                            // Email could not be sent
                            $output["success"]="0";
                            $output["message"]="Email could not be sent";
                            echo json_encode($output);

                            // Log error sending email
                            $log_msg = "Verification email to ". $data->email .", could not be sent.";
                            $log_obj->errorLog($log_msg);
                        }

                        // Output
                        

                        // Log the Registration
                        $log_msg = "New registration request: ". $data->email;
                        $log_obj->infoLog($log_msg);

                    } else{
                        echo 'User could not be created.';
                        // Log the failed Registration
                        $log_msg = "Failed registration: ". $data->email .", User could not be created.";
                        $log_obj->infoLog($log_msg);
                    }
                }else{
                    $output["success"]="-1";
                    $output["message"]="Incorrect Phone Number length";
                    echo json_encode($output);
                }

                
                
            }else{
                //User exists
                $output["success"]="-1";
                $output["message"]="Email already exists, please use another email.";
                echo json_encode($output);
            }

    }else{
        //Data is incomplete
        $output["success"]="0";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }

?>
