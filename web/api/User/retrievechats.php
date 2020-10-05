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

    // Create Chats object
    $chat_obj = new Chat();

    // Create Blocked object
    $blocked_obj = new Blocked();

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

                // Get the other chat participent
                $stmnt_participant = $chat_obj->getOtherParticipent($chat_id['CHAT_ID'],$data->user_id);
                $participant_row = $stmnt_participant->fetch(PDO::FETCH_ASSOC);

                // Check if current user has blocked the recipient user
                if( $blocked_obj->checkBlocked($data->user_id,$participant_row['USER_ID']) ){
                    // Only retrieve most recent message before being blocked 

                    // Get the recent message from the chat
                    $stmnt_msg = $chat_obj->getRecentMessageBlocked($chat_id['CHAT_ID'],$data->user_id,$participant_row['USER_ID']);
                    $stmt_msg_count = $stmnt_msg->rowCount();

                    // If the chat has messages (it should)
                    if( $stmt_msg_count > 0 ){
                        // Get the message
                        $message_row = $stmnt_msg->fetch(PDO::FETCH_ASSOC);
                        $message_row['FULL_NAME'] = $participant_row['FULL_NAME'];
                        array_push($result_arr,$message_row);
                    }

                }else{
                    // Current user has not blocked the recipient user

                    // Get the recent message from the chat
                    $stmnt_msg = $chat_obj->getRecentMessage($chat_id['CHAT_ID'],$data->user_id);
                    $stmt_msg_count = $stmnt_msg->rowCount();

                    // If the chat has messages (it should)
                    if( $stmt_msg_count > 0 ){
                        // Get the message
                        $message_row = $stmnt_msg->fetch(PDO::FETCH_ASSOC);
                        $message_row['FULL_NAME'] = $participant_row['FULL_NAME'];
                        array_push($result_arr,$message_row);
                    }

                }

            }

            // Sort the array  
            usort($result_arr, 'date_compare'); 

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

    // Comparison function DESC
    function date_compare($element1, $element2) { 
        $datetime1 = strtotime($element1['MESSAGE_TIMESTAMP']); 
        $datetime2 = strtotime($element2['MESSAGE_TIMESTAMP']); 
        return $datetime2 - $datetime1; 
    }  

?>