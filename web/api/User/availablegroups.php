<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // Configuration for file url
    require_once __DIR__."/../../config.php";

    include_once SITE_ROOT.'/config/database.php';
    include_once SITE_ROOT.'/class/user.php';
    include_once SITE_ROOT.'/class/group.php';

    $database = new Database();
    $db = $database->getConnection();

    // // Create user object
    // $user_obj = new User($db);
    // Create group object
    $group_obj = new Group($db);

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