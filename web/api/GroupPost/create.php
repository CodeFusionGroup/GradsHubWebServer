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
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group_post.php';

    // Create Group object
    $group_obj = new Group();
    // Create Group Post object
    $group_post_obj = new GroupPost();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if(isset($data->group_id,$data->user_id,$data->post_title,$data->post_date)){

        // retrieve group member
        $group_member = $group_obj->getGroupMember($data->user_id,$data->group_id);

        // Set the group property values
        $group_post_obj->group_user_id = $group_member["GROUP_USER_ID"];
        $group_post_obj->group_id = $data->group_id;
        $group_post_obj->title = $data->post_title;
        $group_post_obj->date = $data->post_date;

        // Check if its a file or url
        if(isset($data->post_url)){
            $group_post_obj->url = $data->post_url;
            // Create the post
            if($group_post_obj->createPostUrl()){
                $output["success"]="1";
                $output["message"]="New post created";
                echo json_encode($output);
            }else{
                echo 'Post could not be created.';
            }
        }
        else if(isset($data->post_file,$data->post_file_name)){
            $group_post_obj->file = $data->post_file;
            $group_post_obj->file_name = $data->post_file_name;
            // Create the post
            if($group_post_obj->createPostFile()){
                $output["success"]="1";
                $output["message"]="New post created";
                echo json_encode($output);
            }else{
                echo 'Post could not be created.';
            }
        }else{
            $output["success"]="0";
            $output["message"]="Values needed to upload file are missing!";
            echo json_encode($output);
        }
        
    }else{
        $output["success"]="0";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }


?>