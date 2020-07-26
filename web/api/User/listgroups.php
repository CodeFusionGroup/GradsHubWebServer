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

    // Create user object
    $user_obj = new User($db);
    // Create group object
    $group_obj = new Group($db);

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    $stmnt = $user_obj->getUserByEmail($data->user_email);
    $user_count = $stmnt->rowCount();

    // Check if user email exists
    if($user_count > 0){

        $data_row = $stmnt->fetch(PDO::FETCH_ASSOC);
        $user_id = $data_row["USER_ID"];

        $user_groups = $group_obj->getUserGroups($user_id);
        $user_groups_count = $user_groups->rowCount();

        if($user_groups_count>0){

            $group_arr = array();

            $output["success"] = "1";
            while($row = $user_groups->fetch(PDO::FETCH_ASSOC) ){
                extract($row);
                array_push($group_arr,$row);
            }
            $output["message"] = $group_arr ;
            echo json_encode($output);

        }else{
            $output["success"] = "0";
            $output["message"] = "You have not joined any groups";
            echo json_encode($output);
        }

    }else{
        $output["success"] = "-1";
		$output["message"] = "Email doesn't exist, please try again";
		echo json_encode($output);
    }

?>