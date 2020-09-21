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

    if(isset($data->user_id)){

        // Fetch all friends
        $stmnt = $friend_obj->findAcceptedFriends($data->user_id);
        $stmnt_count = $stmnt->rowCount();
        
        if($stmnt_count > 0){

            $friends = array();

            while( $row = $stmnt->fetch(PDO::FETCH_ASSOC) ){
                extract($row);
                array_push($friends,$row);
            }

            $output["success"]="1";
            $output["message"]=$friends;
            echo json_encode($output);

        }else{

            $output["success"]="0";
            $output["message"]="No friends";
            echo json_encode($output);

        }


    }else{

        //Data is incomplete
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);

    }

?>
