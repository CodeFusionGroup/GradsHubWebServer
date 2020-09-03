<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for file url
    require_once __DIR__."/../../config.php";

    include_once SITE_ROOT.'/config/database.php';
    include_once SITE_ROOT.'/class/message.php';

    $database = new Database();
    $db = $database->getConnection();

    // Create group object
    $message_obj = new Message($db);

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