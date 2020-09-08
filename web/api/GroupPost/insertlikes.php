<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the Group and Group Post Like class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group_post_like.php';

    // Create group post like object
    $group_post_like = new GroupPostLike();
    // Create group object
    $group_obj = new Group();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if(isset($data->user_id,$data->group_id,$data->post_id)){

        // Store string post_ids in an array
        $post_id_arr = explode(',',$data->post_id);

        // retrieve group member
        $group_member = $group_obj->getGroupMember($data->user_id,$data->group_id);

        // Output array
        $output = array();

        //Used for error checking
        $already_liked = false;

        foreach($post_id_arr as $post_id){

            // Find out if user has already liked post
            if( $group_post_like->checkPostLiked($post_id,$group_member["GROUP_USER_ID"]) ){
                $display["success"] = "0";
                $display["message"] = "You have already liked this post.";
                array_push($output,$display);
                $already_liked = true;
            }else{

                // Set group post like property values
                $group_post_like->group_post_id = $post_id;
                $group_post_like->group_user_id = $group_member["GROUP_USER_ID"];
                $group_post_like->post_like =  "true";

                // Insert the like
                if($group_post_like->create()){
                    $display["success"] = "1";
                    $display["message"] = "Successfully liked the post.";
                    array_push($output,$display);
                }else{
                    // Debugging purposes
                    echo 'Like could not be created.';
                }
            }
        }

        // Output the result 
        // echo json_encode($output);
        if(!$already_liked){
            $message["success"] = "1";
            $message["message"] = "Successfully liked the post.";
            echo json_encode($message);
        }else{
            $message["success"] = "0";
            $message["message"] = "One or more posts where already liked.";
            echo json_encode($message);
        }
        

    }else{
        // Debugging purposes
        echo 'Data is missing';
    }
    
?>