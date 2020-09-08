<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the Event class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/event.php';

    // Create Event object
    $event_obj = new Event();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    if(isset($data->user_id, $data->event_ids)){

        // Store string event_ids in an array
        $event_id_arr = explode(',',$data->event_ids);

        // Output array
        $output = array();

        // Used for error checking
        $already_favourited = false;

        foreach($event_id_arr as $event_id){

            // Set event property values
            $event_obj->event_id = $event_id;

            // ########### Check if the event already exists in the db ###########
            if($event_obj->checkEventExist()){

                // Get and set the auto generated event id
                $stmnt_event_ID = $event_obj->getEventID();
                $data_row = $stmnt_event_ID->fetch(PDO::FETCH_ASSOC); 
                $event_obj->id = $data_row['ID']; 

                // Find out if user has already favourited event
                if($event_obj->checkEventFavourite($data->user_id)){
                    $display["success"] = "0";
                    $display["message"] = "You have already favourited this event.";
                    array_push($output,$display);
                    $already_favourited = true;
                }else{
                    // Insert the favourite
                    if($event_obj->createFavouriteEvent($data->user_id)){
                        $display["success"] = "1";
                        $display["message"] = "Event favourite created";
                        array_push($output,$display);
                    }else{
                        // Debugging purposes
                        echo 'Event could not be updated.';
                    }
                }

            }
            // ########### EVENT DOESNT EXIST, CREATE EVENT ###########
            else{

                // Create the event
                if($event_obj->createEvent()){

                    // Get and set the auto generated event id
                    $stmnt_event_ID = $event_obj->getEventID();
                    $data_row = $stmnt_event_ID->fetch(PDO::FETCH_ASSOC); 
                    $event_obj->id = $data_row['ID']; 

                    // Find out if user has already favourited event
                    if($event_obj->checkEventFavourite($data->user_id)){
                        $display["success"] = "0";
                        $display["message"] = "You have already favourited this event.";
                        array_push($output,$display);
                        $already_favourited = true;
                    }else{
                        // Insert the favourite
                        if($event_obj->createFavouriteEvent($data->user_id)){
                            $display["success"] = "1";
                            $display["message"] = "Event favourite created";
                            array_push($output,$display);
                        }else{
                            // Debugging purposes
                            echo 'Event could not be updated.';
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
        if(!$already_favourited){
            $message["success"] = "1";
            $message["message"] = "Events have been favourited.";
            echo json_encode($message);
        }else{
            $message["success"] = "0";
            $message["message"] = "One or more of your events have been favourited.";
            echo json_encode($message);
        }
        

    }else{
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }

?>