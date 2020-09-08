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

    // Create Message object
    $message_obj = new Message();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if(isset($data->sender_id,$data->recipient_id,$data->date,$data->text)){

        // Set the message property values
        $message_obj->sender_id = $data->sender_id;
        $message_obj->recipient_id = $data->recipient_id;
        $message_obj->date = $data->date_sent;
        $message_obj->text = $data->message_text;

        // Create the message
        if($event_obj->createMessage()){
            $output["success"]="1";
            $output["message"]="Message Sent";
            echo json_encode($output);
        }else{
            $output["success"]="0";
            $output["message"]="Error Sending Message";
            echo json_encode($output);
        }
    }else{
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }


?>