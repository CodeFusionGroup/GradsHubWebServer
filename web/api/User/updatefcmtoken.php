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

    // Create User object
    $user_obj = new User();

    // Create Log object
    $log_obj = new Log();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Check if data is sent
    if(isset($data->user_id,$data->fcm_token)){

        // Set User object property values
        $user_obj->id = $data->user_id;
        $user_obj->fcm_token = $data->fcm_token;

        if($user_obj->updateFCMToken()){
            $output["success"]="1";
            $output["message"]="Token updated";
            echo json_encode($output);

            // Log token changed
            $res = $user_obj->getFullName($data->user_id);
            $res_store = $res->fetch(PDO::FETCH_ASSOC);
            $fullname = $res_store['USER_FNAME'] . " " . $res_store['USER_LNAME'];
            $log_msg = " User: ". $fullname . ", has updated device token.";
            $log_obj->infoLog($log_msg);
        }else{
            $output["success"]="0";
            $output["message"]="Could not update token";
            echo json_encode($output);
        }

    }else{
        //Data is incomplete
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }
