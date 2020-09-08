<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the User class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group.php';

    // // Create user object
    // $user_obj = new User($db);
    // Create Group object
    $group_obj = new Group();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    $stmnt = $group_obj->getAvailableGroups($data->user_id);
    $stmnt_count = $stmnt->rowCount();

    // If there are any available groups
    if($stmnt_count>0){

        $group_arr = array();
        $output["success"] = "1";

        while($row = $stmnt->fetch(PDO::FETCH_ASSOC) ){
            extract($row);
            array_push($group_arr,$row);
        }

        $output["message"] = $group_arr ;
        echo json_encode($output);

    }else{
        $display["success"] = "0";
        $display["message"] = "No available groups.";
        echo json_encode($display);
        mysqli_close($link);
    }
?>