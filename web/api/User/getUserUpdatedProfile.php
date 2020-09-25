<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the User class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/user.php';

    // Create User object
    $user_obj = new User();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    $stmnt = $user_obj->getUserProfile($data->user_id);
    $stmnt_count = $stmnt->rowCount();

    if($stmnt_count>0){
        $profile = array();
        $output["success"] = "1";

        while($row = $stmnt->fetch(PDO::FETCH_ASSOC) ){
            extract($row);
            array_push($profile,$row);
        }
        $output["message"] = $profile ;
        echo json_encode($output);

    }else{
        $output["success"] = "0";
        $output["message"] = "user does not exist.";
        echo json_encode($output);
    }


?>