<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the User class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/event.php';

    // Create group object
    $event_obj = new Event();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if(isset($data->user_id,$data->event_ids)){

        // Store string event_ids in an array
        $event_id_arr = explode(',',$data->event_ids);

        // Output array
        $output = array();

        // Used for error checking
        $inserted_count =0;

        foreach($event_id_arr as $event_id){

            // Set event property values
            $event_obj->event_id = $event_id;

            // Get and set the auto generated event ID
            $stmnt_event_ID = $event_obj->getEventID();
            $data_row = $stmnt_event_ID->fetch(PDO::FETCH_ASSOC); 
            $event_obj->id = $data_row['ID']; 

            // Update the favourite to unstar event
            if($event_obj->updateFavourite($data->user_id)){
                $display["success"] = "1";
                $display["message"] = "Event favourite created";
                array_push($output,$display);
                // Count number of times we successfully insert
                $inserted_count++;

            }else{
                // Debugging purposes
                echo 'Event could not be updated.';
            }
        }

        if($inserted_count == sizeof($event_id_arr) ){
            // Output the result 
            $message["success"] = "1";
            $message["message"] = "Events have been unfavourited.";
            echo json_encode($message);
        }else{
            $message["success"] = "0";
            $message["message"] = "One of more events were not successfully unfavourited";
            echo json_encode($message);
        }

    }else{
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }

?>