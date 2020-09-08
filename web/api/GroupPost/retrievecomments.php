<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the Group Post Comment class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group_post_comment.php';

    // Create Group Post Comment object
    $group_post_comment = new GroupPostComment();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set group post comment property values
    $group_post_comment->group_post_id = $data->post_id;

    // Retrieve all comments for a post
    $stmnt = $group_post_comment->readAll();
    $stmnt_count = $stmnt->rowCount();

    if($stmnt_count>0){
        $comments_arr = array();
        $output["success"] = "1";

        while($row = $stmnt->fetch(PDO::FETCH_ASSOC) ){
            extract($row);
            array_push($comments_arr,$row);
        }
        $output["message"] = $comments_arr ;
        echo json_encode($output);

    }else{
        $output["success"] = "0";
        $output["message"] = "This post has no comments";
        echo json_encode($output);
    }

?>