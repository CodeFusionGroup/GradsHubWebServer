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
   
    //Mocking database for testing purpose
    if($data->email == "tester141414fhfvbd@gmail.com"){
    	//Test registration success
		$output["success"]="-1";
		$output["message"]="Registration test successful!";
		echo json_encode($output);
    }

    // Make sure data is not empty
    elseif(isset($data->f_name,$data->l_name,$data->password,
        $data->email,$data->phone_no,$data->acad_status)){
            //,$data->fcm_token

            // Ensure User does not already exist
            $user_query = $user_obj->getUserByEmail($data->email);
            $count_user_query = $user_query->rowCount();
            if($count_user_query == 0 ){

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

                // Create the user
                if($user_obj->createUser()){
                    // echo 'User created successfully.';
                    $output["success"]="1";
                    $output["message"]="Registration successful!";
                    echo json_encode($output);
                } else{
                    echo 'User could not be created.';
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
