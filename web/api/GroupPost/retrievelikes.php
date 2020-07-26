<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    
    // Configuration for file url
    require_once __DIR__."/../../config.php";

    include_once SITE_ROOT.'/config/database.php';
    include_once SITE_ROOT.'/class/group_post_like.php';

    $database = new Database();
    $db = $database->getConnection();

    // Create group object
    $group_post_like = new GroupPostLike($db);

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Retrieve all user likes for a post
    $stmnt = $group_post_like->readUserLikes($data->user_id,$data->group_id);
    $stmnt_count = $stmnt->rowCount();

    if($stmnt_count>0){
        $likes_arr = array();
        $output["success"] = "1";

        while($row = $stmnt->fetch(PDO::FETCH_ASSOC) ){
            extract($row);
            array_push($likes_arr,$row);
        }
        $output["message"] = $likes_arr ;
        echo json_encode($output);

    }else{
        $output["success"] = "0";
        $output["message"] = "You have not liked any posts.";
        echo json_encode($output);
    }

?>