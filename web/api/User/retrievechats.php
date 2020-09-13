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
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/user.php';

    // Create Message object
    $chat_obj = new Chat();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if(isset($data->user_id)){

        $stmnt_chats = $chat_obj->getOpenChats($data->user_id);
        $stmnt_chats_count = $stmnt_chats->rowCount();

        if($stmnt_chats_count>0){

            $open_chats = array();

            while( $row = $stmnt_chats->fetch(PDO::FETCH_ASSOC) ){
                extract($row);
                array_push($open_chats,$row);
            }

            $result_arr = array();
            $messages = array();
            foreach($open_chats as $chat_id){

                $stmnt_msg = $chat_obj->getRecentMessage($chat_id['CHAT_ID'],$data->user_id);
                $stmt_msg_count = $stmnt_msg->rowCount();

                // If the chat has messages
                if( $stmt_msg_count > 0 ){
                    // Message exists
                    $dataRow = $stmnt_msg->fetch(PDO::FETCH_ASSOC);
                    array_push($result_arr,$dataRow);
                }
            }

            $output["success"] = "1";
            $output["message"] = $result_arr ;
            echo json_encode($output);

        }else{
            $output["success"] = "0";
            $output["message"] = "No open chats";
            echo json_encode($output);
        }

    }else{
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }


?>