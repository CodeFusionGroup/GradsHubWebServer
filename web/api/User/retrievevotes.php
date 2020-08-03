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

    // Create user object
    $event_obj = new Event($db);

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Get the events a user has liked/disliked(voted)
    $user_events_voted = $event_obj->getUserEventVotes($data->user_id);
    $user_events_voted_count = $user_events_voted->rowCount();

    if($user_events_voted_count>0){

        $event_arr = array();

        $output["success"] = "1";
        while($row = $user_events_voted->fetch(PDO::FETCH_ASSOC) ){
            extract($row);
            array_push($event_arr,$row);
        }
        $output["message"] = $event_arr ;
        echo json_encode($output);

    }else{
        $output["success"] = "0";
        $output["message"] = "You have not voted on any events";
        echo json_encode($output);
    }

?>