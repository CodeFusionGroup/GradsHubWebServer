<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/event.php';

    // Create Event object
    $event_obj = new Event();

    $stmnt_events = $event_obj->fetchStarCount();
    $stmnt_events_count = $stmnt_events->rowCount();

    if($stmnt_events_count > 0){

        $display = array();
        // Fetch the star counts
        while ( $row = $stmnt_events->fetch(PDO::FETCH_ASSOC) ){
            extract($row);
            // Push info into an array
            array_push($display,$row);
        }
        $output["success"] = "1";
        $output["message"] = $display;
        echo json_encode($output);

    }else{
        $output["success"] = "0";
        $output["message"] = "No events have been starred";
        echo json_encode($output);
    }

?>