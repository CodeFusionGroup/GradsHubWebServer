<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the classes
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/message.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/user.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/chat.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/blocked.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/log.php';
    // Get Required files for Push Notification 
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/push.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/firebase.php';

    // Create Message object
    $message_obj = new Message();
    // Create User object
    $user_obj = new User();
    // Create firebase object 
    $firebase_obj = new Firebase(); 
    // Create a Chat object
    $chat_obj = new Chat();
    // Create a Blocked object
    $blocked_obj = new Blocked();
    // Create Log object
    $log_obj = new Log();
    
    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));


    // Make sure data is not empty
    if(isset($data->sender_id,$data->recipient_id,$data->message_timestamp,$data->message_text)){

        // ########## CHAT ##########

        // Set the Chat property values
        $chat_obj->name = $data->sender_id . ":" . $data->recipient_id;
        $chatCreated = false;
        $chat_id = null;

        //Create chat name(s) to check
        $chat_name1 = $data->sender_id . ":" . $data->recipient_id;
        $chat_name2 = $data->recipient_id . ":" . $data->sender_id;

        //Check that a chat exists
        if(!$chat_obj->chatExist($chat_name1,$chat_name2)){

            // Create the chat
            if($chat_obj->createChat()){

                // Create the chat participants
                if( $chat_obj->createChatParticipant($data->sender_id) 
                    && $chat_obj->createChatParticipant($data->recipient_id)){

                    $chatCreated = true;
                    $chat_id = $chat_obj->id;

                    // Log added participants to chat
                    $log_msg = "{Create Message} Added users ". $data->sender_id ." and ". $data->recipient_id ." as participants to chat ". $chat_obj->id;
                    $log_obj->errorLog($log_msg);

                }else{
                    $output["success"]="0";
                    $output["message"]="Could not create a new chat";
                    echo json_encode($output);

                    // Log could not add chat participants
                    $log_msg = "{Create Message} Could not add users ". $data->sender_id ." and ". $data->recipient_id ." as participants to chat ". $chat_obj->id;
                    $log_obj->errorLog($log_msg);
                }

            }else{
                $output["success"]="0";
                $output["message"]="Could not create a new chat";
                echo json_encode($output);

                // Log could not create chat
                $log_msg = "{Create Message} User: ".$data->sender_id.", could not create chat ".$chat_obj->id;
                $log_obj->errorLog($log_msg);

            }

        }else{
            // Chat already exists
            $chatCreated = true;
            $chat_id = $chat_obj->id;

            // Check if the chat is open/closed
            if( !$chat_obj->checkOpen($chat_obj->id,$data->sender_id) ){
                // Chat is closed, open it.
                if( !$chat_obj->openChat($chat_obj->id,$data->sender_id) ){
                    // Chat could not be opened

                    // Log could not open chat error
                    $log_msg = "{Create Message} User: ".$data->sender_id.", could not open chat ".$chat_obj->id;
                    $log_obj->errorLog($log_msg);
                }else{
                    // Chat was reopened

                    // Log reopening chat
                    $log_msg = "{Create Message} User: ".$data->sender_id.", re-opened chat ".$chat_obj->id;
                    $log_obj->infoLog($log_msg);
                }
            }
        }
        
        // Check if chat is created already
        if($chatCreated){

            // ########## MESSAGING ##########
            // Set the message property values
            $message_obj->sender_id = $data->sender_id;
            $message_obj->chat_id = $chat_id;
            $message_obj->timestamp = $data->message_timestamp;
            $message_obj->text = $data->message_text;

            //Get the sender's fullname
            $stmnt_names = $user_obj->getFullName($data->sender_id);
            $dataRow = $stmnt_names->fetch(PDO::FETCH_ASSOC);
            $fullname = $dataRow['USER_FNAME'] . " " . $dataRow['USER_LNAME'];

            // Create the message in the database
            if($message_obj->createMessage()){

                // If sender is blocked don't create push notification
                if($blocked_obj->checkBlocked($data->recipient_id,$data->sender_id)){
                    
                    // Sender is blocked by recipient
                    $output["success"]="1";
                    $output["message"]="Message Sent";
                    echo json_encode($output);

                    // Log the sent message
                    $log_msg = "{Create Message} Blocked User: ". $data->sender_id . ", sent a message to user: ". $data->recipient_id;
                    $log_obj->infoLog($log_msg);

                }else{

                    // Sender is not blocked

                    // Create Push Object
                    $push_obj = new Push($fullname,$data->message_text);
                    // Get the Push Notification from the push object
                    $data_payload = $push_obj->getMessage();
                    $notification_payload = $push_obj->getNotification();
                    // Get the token for the recipients device
                    $recipient_token = $user_obj->getTokenByID($data->recipient_id);
                    // Send Push Notification
                    $firebase_obj->send($recipient_token, $data_payload,$notification_payload);
                    // Log the push notification
                    $log_msg = "Push notification sent to user: ". $data->recipient_id .", to device: ". $recipient_token[0];
                    $log_obj->infoLog($log_msg);
                    
                    $output["success"]="1";
                    $output["message"]="Message Sent";
                    echo json_encode($output);

                    // Log the sent message
                    $log_msg = "{Create Message} User: ". $data->sender_id . ", sent a message to user: ". $data->recipient_id;
                    $log_obj->infoLog($log_msg);
                }
               
            }else{
                $output["success"]="0";
                $output["message"]="Error Sending Message";
                echo json_encode($output);
            }

        }else{
            // Chat was not created
            $output["success"]="0";
            $output["message"]="Could not create a new chat";
            echo json_encode($output);
        }
        
    }else{
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }


?>