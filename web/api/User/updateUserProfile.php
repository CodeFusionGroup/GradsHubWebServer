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
    if(isset($data->f_name,$data->l_name,
        $data->email,$data->phone_no,$data->acad_status)){

            // Ensure User does not already exist
            $user_query = $user_obj->getUserByEmail($data->email);
            $count_user_query = $user_query->rowCount();
            if($count_user_query == 0 ){

                // Check that phone number is 10 digits
                if(strlen($data->phone_no) == 10){

                    // Set the user property values
                    $user_obj->f_name = $data->f_name;
                    $user_obj->l_name = $data->l_name;
                    $user_obj->email = $data->email;
                    $user_obj->phone_no = $data->phone_no;
                    $user_obj->acad_status = $data->acad_status;
                    

                    // Create the user
                    if($user_obj->updateUserProfile($data->user_id)){
                        $output["success"]="1";
                        $output["message"]="Update successful!";
                        echo json_encode($output);
                    } else{
                        echo 'could not update profile.';
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