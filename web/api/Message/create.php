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
    // Get Required files for Push Notification 
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/push.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/firebase.php';

    // Create Message object
    $message_obj = new Message();
    // Create User object
    $user_obj = new User();
    // Create firebase object 
    $firebase_obj = new Firebase(); 
    
    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if(isset($data->sender_id,$data->recipient_id,$data->date_sent,$data->message_text)){

        // Set the message property values
        $message_obj->sender_id = $data->sender_id;
        $message_obj->recipient_id = $data->recipient_id;
        $message_obj->date = $data->date_sent;
        $message_obj->text = $data->message_text;

        //Get the User's fullname
        $stmnt_names = $user_obj->getFullName($data->recipient_id);
        $dataRow = $stmnt_names->fetch(PDO::FETCH_ASSOC);
        $fullname = $dataRow['USER_FNAME'] . " " . $dataRow['USER_LNAME'];

        
        // Create the message in the database
        if($message_obj->createMessage()){

            // Create Push Object
            $push_obj = new Push($fullname,$data->message_text);
            // Get the Push Notification from the push object
            $push_notification = $push_obj->getPush();
            // $test_not = $push_obj->testNot();
            // Get the token for the recipients device
            $recipient_token = $user_obj->getTokenByID($data->recipient_id);
            // Send Push Notification
            echo $firebase_obj->send($recipient_token, $push_notification);
            // echo $firebase_obj->notification($recipient_token,$test_not);
            
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