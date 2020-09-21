<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the User class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/blocked.php';

    // Create User object
    $blocked_obj = new Blocked();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    if( isset($data->user_id,$data->blocked_user_id) ){

        // Set the Blocked property values
        $blocked_obj->user_id = $data->user_id;
        $blocked_obj->blocked_user_id = $data->blocked_user_id;

        //TODO:
        if($blocked_obj->unblockUser2()){
        // if($blocked_obj->unblockUser()){

            $output["success"]="1";
            $output["message"]="User unblocked";
            echo json_encode($output);

        }else{
            $output["success"]="0";
            $output["message"]="User couldn't be unblocked";
            echo json_encode($output);
        }

    }else{

        //Data is incomplete
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }