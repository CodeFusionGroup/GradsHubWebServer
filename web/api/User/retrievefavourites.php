<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for file url
    require_once __DIR__."/../../config.php";

    include_once SITE_ROOT.'/config/database.php';
    include_once SITE_ROOT.'/class/event.php';

    $database = new Database();
    $db = $database->getConnection();

    // Create group object
    $event_obj = new Event($db);

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    $stmnt = $event_obj->getUserEventFavourite($data->user_id);
    $stmnt_count = $stmnt->rowCount();

    if($stmnt_count>0){
        $fav = array();
        $output["success"] = "1";

        while($row = $stmnt->fetch(PDO::FETCH_ASSOC) ){
            extract($row);
            array_push($fav,$row);
        }
        $output["message"] = $fav ;
        echo json_encode($output);

    }else{
        $output["success"] = "0";
        $output["message"] = "You have not favourited an event.";
        echo json_encode($output);
    }

?>