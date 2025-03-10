<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the classes
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/chat.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/log.php';

    // Create Chat object
    $chat_obj = new Chat();

    // Create Log object
    $log_obj = new Log();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if(isset($data->user_id,$data->chat_ids)){

        // Store string chat_ids in an array
        $chat_id_arr = explode(',',$data->chat_ids);

        // Chat counter
        $chat_count = 0;

        // Get the chat_ids
        foreach($chat_id_arr as $chat_id){

            // Close each chat chat
            if($chat_obj->closeChat($chat_id,$data->user_id)){
                // $output["success"] = "1";
                // $output["message"] = "Chat closed";
                // echo json_encode($output);
                $chat_count ++;
            }
        }

        // Check if all chats where closed
        if($chat_count == sizeof($chat_id_arr)){
            $output["success"] = "1";
            $output["message"] = "Chats closed";
            echo json_encode($output);

            //Log user has closed chats
            $log_msg = "{Close Chat(s)} User: ". $data->user_id . ", has closed chats :[". $data->chat_ids ."].";
            $log_obj->errorLog($log_msg);
        }else{
            $output["success"] = "0";
            $output["message"] = "One or more chats didn't close.";
            echo json_encode($output);
        }

    }else{
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }
?>