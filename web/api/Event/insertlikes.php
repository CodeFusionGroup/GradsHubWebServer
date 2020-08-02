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

    // Create event object
    $event_obj = new Event($db);

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if(isset($data->user_id,$data->event_id,$data->like)){

        // Set event property values
        $event_obj->id = $data->event_id;

        // TODO: Check if the user has liked/disliked and if one is true allow updating to the other one
        // e.g. if i have liked event A but now want to dislike allow to dislike

        // Find out if user has already liked/disliked event
        if($event_obj->checkEventLiked($data->user_id)){
            $output["success"] = "0";
            $output["message"] = "You have already liked this event.";
            echo json_encode($output);
        }else{

            // Insert the like
            if($event_obj->createUserEvent($data->user_id,$data->like)){
                $output["success"] = "1";
                $output["message"] = "Event liked";
                echo json_encode($output);
            }else{
                // Debugging purposes
                echo 'Like could not be created.';
            }
        }

    }

?>
