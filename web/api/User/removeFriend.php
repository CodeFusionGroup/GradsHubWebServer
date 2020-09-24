<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the User class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/friend.php';

    // Create User object
    $friend_obj = new Friend();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    if(isset($data->user_id,$data->friend_id)){

        //Check if they have a relationship(friendship)
        $stmnt_check = $friend_obj->checkFriendship($data->user_id, $data->friend_id);
        $stmnt_check_count = $stmnt_check->rowCount();

        if($stmnt_check_count > 0){

            // Retrieve Status of friendship
            $data_row = $stmnt_check->fetch(PDO::FETCH_ASSOC);
            $status = $data_row ['FRIEND_STATUS'];

            if($status == 'accepted'){

                // Set Friend property values
                $friend_obj->user_id = $data->user_id;
                $friend_obj->friend_id = $data->friend_id;
                $friend_obj->friend_status = "removed";

                // Remove friendship
                if( $friend_obj->updateFriendship() ){

                    $output["success"]="1";
                    $output["message"]="Friend Removed";
                    echo json_encode($output);

                }else{

                    $output["success"]="0";
                    $output["message"]="Couldn't remove friend";
                    echo json_encode($output);

                }

            }else{

                $output["success"]="0";
                $output["message"]="You are not friends";
                echo json_encode($output);

            }

        }else{

            $output["success"]="0";
            $output["message"]="No existing or prior friendship found";
            echo json_encode($output);
        }

    }else{
        //Data is incomplete
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }