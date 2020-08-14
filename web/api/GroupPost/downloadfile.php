<?php

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
	//including the database connection file
    require_once __DIR__."/../../config.php";

    include_once SITE_ROOT.'/config/database.php';
    include_once SITE_ROOT.'/class/group.php';
    include_once SITE_ROOT.'/class/group_post.php';

    $database = new Database();
    $db = $database->getConnection();

    // Create user object
    $group_post_obj = new GroupPost($db);
    // Create group object
    $group_obj = new Group($db);

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Retrieve all user PDFS for a post
    $stmnt = $group_post_obj->downloadFile($data->group_post_id);
    $stmnt_count = $stmnt->rowCount();

    if($stmnt_count>0){

        $stmnt_row = $stmnt->fetch(PDO::FETCH_ASSOC);
        $file_path = $stmnt_row["POST_ATTACHMENT_FILE"];

        $output["success"] = "1";
        $output["message"] = $file_path;
        echo json_encode($output);

    }else{
        $output["success"] = "0";
        $output["message"] = "File does not exist";
        echo json_encode($output);
    }

?>
