<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the classes
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group_post_like.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group_post.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group.php';

    // Create Group Post Like object
    $group_post_like = new GroupPostLike();

    // Create Group Post object
    $group_post_obj = new GroupPost();

    // Create Group object
    $group_obj = new Group();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    if(isset($data->user_id)){

        // Get all the groups a user belongs to
        $stmnt_groups = $group_obj->getGroups($data->user_id);
        $stmnt_groups_count = $stmnt_groups->rowCount();

        if($stmnt_groups_count > 0){

            // Fetch the groups
            while($group = $stmnt_groups->fetch(PDO::FETCH_ASSOC)){
                
                $group_id = $group["GROUP_ID"];

                // Get the top 4 or less posts from each group
                $stmnt_posts = $group_post_obj->findGroupPosts($group_id);
                $stmnt_posts_count = $stmnt_posts->rowCount();
            }

        }else{
            // No groups
            $output["success"]="0";
            $output["message"]="Please join groups to have a feed.";
            echo json_encode($output);
        }

        // Get the feed
        $stmnt_feed = $group_post_obj->feed($data->user_id); 
        $stmnt_feed_count = $stmnt_feed->rowCount();

        // Check if user has any posts on feed
        if($stmnt_feed_count > 0){

            // Fetch the feed
            $feed_arr = $stmnt_feed->fetch(PDO::FETCH_ASSOC);

            //Array to store the liked posts
            $liked_group_posts = array();

            // Check if user has liked the post
            foreach($feed_arr as $post){

                //Values needed
                $group_post_id = $post['GROUP_POST_ID'];
                $group_id = $post['GROUP_ID'];

                // Find the user's group_user_id
                $group_member = $group_obj->getGroupMember($data->user_id, $group_id);
                $group_user_id = $group_member['GROUP_USER_ID'];

                // Check post liked
                if( $group_post_like->checkPostLiked($group_post_id,$group_user_id ) ){

                    // Append to the array
                    $temp['GROUP_POST_ID'] = $group_user_id;
                    array_push($liked_group_posts,$temp);

                }else{
                    // Just skip
                    continue;
                }
            }

            // Output the result
            $output["success"] = "1";
            $output["message"] = $liked_group_posts ;
            echo json_encode($output);

        }else{

            $output["success"] = "0";
            $output["message"] = "Feed Empty.";
            echo json_encode($output);

        }


    }else{

        //Data is incomplete
        $output["success"]="0";
        $output["message"]="You didn't send the required value!";
        echo json_encode($output);

    }

?>