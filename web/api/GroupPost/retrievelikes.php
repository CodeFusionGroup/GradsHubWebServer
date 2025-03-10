<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the Group Post Like class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group_post_like.php';

    // Create Group Post Like object
    $group_post_like = new GroupPostLike();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if(isset($data->user_id,$data->group_id )){

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

    }else{

        //Data is incomplete
        $output["success"]="0";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);

    }

    

?>