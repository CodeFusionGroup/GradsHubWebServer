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
    if(isset($data->user_id,$data->event_ids,$data->event_votes)){

        // Store string event_ids and event_votes in adjacent arrays
        $event_id_arr = explode(',',$data->event_ids);
        $event_votes_arr = explode(',',$data->event_votes);

        // Output array
        $output = array();

        // TODO: Update for-loop to for-each
        for($i=0;$i<sizeof($event_id_arr);$i++){

            // Set event property values
            $event_obj->event_id = $event_id_arr[$i];
            $vote = $event_votes_arr[$i];

            // TODO: Check if the user has liked/disliked and if one is true allow updating to the other one
            // e.g. if i have liked event A but now want to dislike allow to dislike

            // Used for error checking
            $already_voted = false;

            // ########### Check if the event already exists in the db ###########
            if($event_obj->checkEventExist()){

                // Get and set the auto generated event id
                $stmnt_event_ID = $event_obj->getEventID();
                $data_row = $stmnt_event_ID->fetch(PDO::FETCH_ASSOC); 
                $event_obj->id = $data_row['ID'];  
                
                // Find out if user has already liked/disliked event
                if($event_obj->checkEventLiked($data->user_id)){
                    $display["success"] = "0";
                    $display["message"] = "You have already liked this event.";
                    array_push($output,$display);
                    //
                    $already_voted = true;
                }else{
                    // Insert the like
                    if($event_obj->createEventVote($data->user_id,$vote)){
                        $display["success"] = "1";
                        $display["message"] = "Event vote created";
                        array_push($output,$display);
                    }else{
                        // Debugging purposes
                        echo 'Event Exists:Like could not be created.';
                    }
                }
                

            // ########### EVENT DOESNT EXIST, CREATE EVENT ###########
            }else{

                // Create the event
                if($event_obj->createEvent()){

                    // Get and set the auto generated event id
                    $stmnt_event_ID = $event_obj->getEventID();
                    $data_row = $stmnt_event_ID->fetch(PDO::FETCH_ASSOC); 
                    $event_obj->id = $data_row['ID'];                    

                    // Find out if user has already liked/disliked event
                    if($event_obj->checkEventLiked($data->user_id)){
                        $display["success"] = "0";
                        $display["message"] = "You have already liked this event.";
                        array_push($output,$display);
                        // 
                        $already_voted = true;
                    }else{

                        // Insert the like
                        if($event_obj->createEventVote($data->user_id,$vote)){
                            $display["success"] = "1";
                            $display["message"] = "Event vote created";
                            array_push($output,$display);
                        }else{
                            // Debugging purposes
                            echo 'Event Not Exist:Like could not be created.';
                        }
                    }

                }else{
                    // Debugging purposes
                    echo 'Event could not be created.';
                }
            }
        }

        // Output the result 
        // echo json_encode($output);
        if(!$already_voted){
            $message["success"] = "1";
            $message["message"] = "Votes have been inserted.";
            echo json_encode($message);
        }else{
            $message["success"] = "0";
            $message["message"] = "One or more of the events already have votes.";
            echo json_encode($message);
        }
        

    }else{
        // Debugging purposes
        $output["success"] = "-1";
        $output["message"] = "One or more values are missing.";
        echo json_encode($output);
    }

?>
