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

    // Create Log object
    $email_obj = new Email();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if( isset($data->user_id,$data->first_name,$data->last_name, $data->email,
        $data->phone_no, $data->acad_status)){

            $user_email = null;
            $email_valid = false;

            //Check if email isn't being changed
            if($user_obj->emailsEqual($data->user_id,$data->email)){
                // Email is not being changed
                $user_email = $data->email;
                $email_valid = true;
            }
            // Email is being changed ,check if its unique.
            else{
                
                // Ensure Email doesn't exist(is unique)
                if( $user_obj->checkExists($data->email) ){

                    // Email exists notify user
                    $output["success"]="0";
                    $output["message"]="Email already exists";
                    echo json_encode($output);
                    

                }else{
                    
                    //Email is unique
                    $user_email = $data->email;
                    $email_valid = true;

                    // Unverification properties
                    $code = md5(rand(0,1000));
                    // Verification date/timestamp
                    $date_format = mktime( date("H"), date("i"), date("s"),
                        date("m") ,date("d")+1, date("Y") );
                    $verify_date = date("Y-m-d H:i:s",$date_format);

                    // Make account unverified and send email to user
                    if($user_obj->unverifyUser($data->user_id,$code,$verify_date)){
                        // User is unverified
                        $fullname =  $data->first_name ." ".$data->last_name;

                        //Send email 
                        if($email_obj->changedEmailVerification($user_email, $code, $fullname )){
                            
                            // log email sent
                            $log_msg = "{Update Profile} Verification email sent to ". $user_email;
                            $log_obj->infoLog($log_msg);

                        }else{

                            // Log error sending email
                            $log_msg = "{Update Profile} Verification email to ". $user_email .", could not be sent.";
                            $log_obj->errorLog($log_msg);

                        }

                    }else{
                        // Could not unverify user in db

                        // Log error 
                        $log_msg = "{Update Profile} Could not unverify User: ". $data->email . " in DB.";
                        $log_obj->errorLog($log_msg);

                    }

                    // Log user changing email
                    $res = $user_obj->getFullName($data->user_id);
                    $res_store = $res->fetch(PDO::FETCH_ASSOC);
                    $fullname = $res_store['USER_FNAME'] . " " . $res_store['USER_LNAME'];
                    $log_msg = "{Update Profile} User: ". $fullname . " is changing email to " . $data->email;
                    $log_obj->infoLog($log_msg);
                }
            }

            // check if email is valid first
            if($email_valid){

                // Check that phone number is 10 digits
                if(strlen($data->phone_no) == 10){

                    // Check if password is being updated
                    if( isset($data->password) ){

                        //Hash the password
                        $hashed_password = password_hash($data->password,PASSWORD_DEFAULT);

                        $password_update = false;

                        //Update the password
                        if($user_obj->updatePassword($data->user_id,$hashed_password)){
                            $password_update = true;
                            // Log user changing password
                            $log_msg = "{Update Profile} User: ". $data->email . " is changing their password";
                            $log_obj->infoLog($log_msg);
                        }else{
                            $password_update = false;
                        }
                    }

                    // Check if profile is being updated
                    if( isset($data->profile_picture) ){

                        $pic_update = false;

                        // Update the profile
                        if($user_obj->updateProfilePic($data->user_id,$data->profile_picture)){
                            $pic_update = true;
                        }else{
                            $pic_update = false;
                        }

                    }

                    // Set the user property values
                    $user_obj->id = $data->user_id;
                    $user_obj->fname = $data->first_name;
                    $user_obj->lname = $data->last_name;
                    $user_obj->email = $data->email;
                    $user_obj->phone_no = $data->phone_no;
                    $user_obj->acad_status = $data->acad_status;
                    
                    // update user details
                    if( $user_obj->updateProfile() ){
                        $output["success"]="1";
                        $output["message"]="Update successful!";
                        echo json_encode($output);
                        // Log user changed profile
                        $log_msg = "{Update Profile} User: ". $data->email . " has successfuly updated their profile.";
                        $log_obj->infoLog($log_msg);
                    } else{
                        $output["success"]="0";
                        $output["message"]="Update unsuccessful!";
                        echo json_encode($output);
                    }

                }else{
                    $output["success"]="0";
                    $output["message"]="Incorrect Phone Number length";
                    echo json_encode($output);
                }

            } // END EMAIL VALIDATION
            

    }else{
        //Data is incomplete
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }

?>
