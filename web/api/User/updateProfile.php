<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the User class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/user.php';

    // Create User object
    $user_obj = new User();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if( isset($data->user_id, $data->user_name, $data->email,
        $data->phone_no, $data->acad_status, $data->profile_picture)){

            // Ensure User exists
            if( $user_obj->checkExists($data->email) ){

                // Check that phone number is 10 digits
                if(strlen($data->phone_no) == 10){

                    // Check if password is being updated
                    if( isset($data->password) ){

                        //Hash the password
                        $hashed_password = password_hash($data->password,PASSWORD_DEFAULT);

                        //Update the password
                        $password_update = false;

                        if($user_obj->updatePassword($data->user_id,$hashed_password)){
                            $password_update = true;
                        }else{
                            $password_update = false;
                        }
                    }

                    // Set the user property values
                    $user_obj->id = $data->user_id;
                    $user_obj->user_name = $data->user_name;
                    $user_obj->email = $data->email;
                    $user_obj->phone_no = $data->phone_no;
                    $user_obj->acad_status = $data->acad_status;
                    $user_obj->profile_picture = $data->profile_picture;
                    
                    // update user details
                    if( $user_obj->updateProfile() ){
                        $output["success"]="1";
                        $output["message"]="Update successful!";
                        echo json_encode($output);
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

            }else{
                
                //User doesn't exists
                $output["success"]="-1";
                $output["message"]="User does not exist";
                echo json_encode($output);

            }

    }else{
        //Data is incomplete
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }

?>
