<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for file url
    require_once __DIR__."/../../config.php";

    include_once SITE_ROOT.'/config/database.php';
    include_once SITE_ROOT.'/class/chatroom.php';

    $database = new Database();
    $db = $database->getConnection();

    // Create group object
    $chatroom_obj = new Chatroom($db);

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    $stmnt = $chatroom_obj->getChatroom();
    $stmnt_count = $stmnt->rowCount();

    if($stmnt_count>0){
        $messages = array();
        $output["success"] = "1";

        while($row = $stmnt->fetch(PDO::FETCH_ASSOC) ){
            extract($row);
            array_push($messages,$row);
        }
        $output["message"] = $messages ;
        echo json_encode($output);

    }else{
        $output["success"] = "0";
        $output["message"] = "conversation empty";
        echo json_encode($output);
    }

?>