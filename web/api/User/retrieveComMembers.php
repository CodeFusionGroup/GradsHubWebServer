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
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/friend.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/blocked.php';

    // Create Message object
    $message_obj = new Message();
    // Create Friend object
    $friend_obj = new Friend();
    // Create Blocked object
    $blocked_obj = new Blocked();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    if(isset($data->user_id)){

        $stmnt = $message_obj->retrieveCommonGroupUsers($data->user_id);
        $stmnt_count = $stmnt->rowCount();

        if($stmnt_count>0){

            $members = array();
            $output["success"] = "1";

            while($row = $stmnt->fetch(PDO::FETCH_ASSOC) ){
                extract($row);
                // Retrieve the common member's user_id
                $com_mem_user_id = $row['USER_ID'];

                // **** Check if FRIENDSHIP exists ****
                $stmnt_friendship = $friend_obj->checkFriendship($data->user_id,$com_mem_user_id);
                $stmnt_friendship_count = $stmnt_friendship->rowCount();

                if($stmnt_friendship_count > 0){

                    //Check status of friendship
                    $stmnt_friendship_res = $stmnt_friendship->fetch(PDO::FETCH_ASSOC);
                    $friendship_status = $stmnt_friendship_res['FRIEND_STATUS'];

                    switch($friendship_status ){
                        case 'accepted':
                            $row['FRIEND'] = 'true';
                            break;
                        case 'removed':
                            $row['FRIEND'] = 'false';
                            break;
                    }

                }else{
                    $row['FRIEND'] = 'false';
                }

                // **** Check if user is BLOCKED ****
                if( $blocked_obj->checkBlocked($data->user_id,$com_mem_user_id) ){
                    $row['BLOCKED'] = 'true';
                }else{
                    $row['BLOCKED'] = 'false';
                }

                array_push($members,$row);
            }
            $output["message"] = $members ;
            echo json_encode($output);

        }else{
            $output['success'] = "0";
            $output["message"]= "No users. Join a group to start a chat.";
            echo json_encode($output); 
        }

    }else{
        $output['success'] = "-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output); 
    }

?>