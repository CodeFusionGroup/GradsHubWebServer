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
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/blocked.php';

    // Create Chat object
    $chat_obj = new Chat();

    // Create Blocked object
    $blocked_obj = new Blocked();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if(isset($data->user_id_one,$data->user_id_two)){

        //Create chat name(s) to check
        $chat_name1 = $data->user_id_one . ":" . $data->user_id_two;
        $chat_name2 = $data->user_id_two . ":" . $data->user_id_one;

        // Check if the chat exists
        if( $chat_obj->chatExist($chat_name1,$chat_name2) ){
            
            // Check whether current user has blocked the other user
            if($blocked_obj->checkBlocked($data->user_id_one,$data->user_id_two)){
                // Current user has blocked other user so only recieve messages before being blocked

                $stmnt = $chat_obj->getMessagesBlocked($data->user_id_one,$data->user_id_two);
                $stmnt_count = $stmnt->rowCount();

                if($stmnt_count>0){

                    //Output
                    $output["success"] = "1";
                    $messages_arr = array();

                    while($row = $stmnt->fetch(PDO::FETCH_ASSOC) ){
                        extract($row);
                        array_push($messages_arr,$row);
                    }

                    $output["message"] = $messages_arr;
                    echo json_encode($output);

                }else{
                    $output["success"] = "0";
                    $output["message"] = "No Messages";
                    echo json_encode($output);
                }


            }else{
                // Current user is not blocked so retrieve all messages

                $stmnt = $chat_obj->getMessages();
                $stmnt_count = $stmnt->rowCount();

                if($stmnt_count>0){

                    //Output
                    $output["success"] = "1";
                    $messages_arr = array();

                    while($row = $stmnt->fetch(PDO::FETCH_ASSOC) ){
                        extract($row);
                        array_push($messages_arr,$row);
                    }

                    $output["message"] = $messages_arr;
                    echo json_encode($output);

                }else{
                    $output["success"] = "0";
                    $output["message"] = "No Messages";
                    echo json_encode($output);
                }
            }

        }else{
            $output["success"] = "0";
            $output["message"] = "Chat doesn't exist";
            echo json_encode($output);
        }

    }else{
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }

?>