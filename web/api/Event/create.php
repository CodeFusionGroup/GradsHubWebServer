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

    // Make sure data is not empty
    if(isset($data->title)){

        // Set the event property values
        $event_obj->title = $data->title;

        // Create the group
        if($event_obj->createEvent()){

            $output["success"]="1";
            $output["message"]="New event created";
            echo json_encode($output);
            
        }else{
            $output["success"]="1";
            $output["message"]="Event already exists";
            echo json_encode($output);
        }
    }else{
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }


?>