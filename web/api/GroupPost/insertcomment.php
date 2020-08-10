<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for file url
    require_once __DIR__."/../../config.php";

    include_once SITE_ROOT.'/config/database.php';
    include_once SITE_ROOT.'/class/group.php';
    include_once SITE_ROOT.'/class/group_post_comment.php';

    $database = new Database();
    $db = $database->getConnection();

    // Create group post comment object
    $group_post_comment = new GroupPostComment($db);
    // Create group object
    $group_obj = new Group($db);

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if(isset($data->user_id,$data->group_id,$data->post_id,$data->post_comment,$data->post_date)){

        // retrieve group member
        $group_member = $group_obj->getGroupMember($data->user_id,$data->group_id);

        // Set group post comment property values
        $group_post_comment->group_post_id = $data->post_id;
        $group_post_comment->group_user_id = $group_member["GROUP_USER_ID"];
        $group_post_comment->comment = $data->post_comment;
        $group_post_comment->comment_date = $data->post_date;

        // Create the comment
        if($group_post_comment->create()){
            $output["success"]="1";
            $output["message"]="New comment created";
            echo json_encode($output);
        }else{
            // Debugging purposes
            echo 'Comment could not be created.';
        }

    }else{
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }

?>