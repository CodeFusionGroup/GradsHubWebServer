<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the classes
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group_post.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group_post_like.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group_post_comment.php';

    // Create Group object
    $group_obj = new Group();
    // Create Group Post object
    $group_post_obj = new GroupPost();
    // Create Group Post Like object
    $group_post_like = new GroupPostLike();
    // Create Group Post Comment object
    $group_post_comment = new GroupPostComment();
 
    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure value(s) are sent
    if(isset($data->user_id)){

        // Get all the groups a user belongs to
        $stmnt_groups = $group_obj->getGroups($data->user_id);
        $stmnt_groups_count = $stmnt_groups->rowCount();

        if($stmnt_groups_count > 0){

            // Array to hold the posts
            $final_posts_arr = array();

            // Fetch the groups
            while($group = $stmnt_groups->fetch(PDO::FETCH_ASSOC)){

                $group_id = $group["GROUP_ID"];

                // Get the top 4 or less posts from each group
                $stmnt_posts = $group_post_obj->findGroupPosts($group_id);
                $stmnt_posts_count = $stmnt_posts->rowCount();

                if($stmnt_posts_count > 0){

                    // Variables needed
                    $post = array();

                    // Fetch the posts
                    while($post = $stmnt_posts->fetch(PDO::FETCH_ASSOC) ){
                        
                        // Get the likes and comments for the post
                        $stmnt_likes = $group_post_like->getNoOfLikes($post['GROUP_POST_ID']);
                        $stmnt_comments = $group_post_comment->getNoOfComments($post['GROUP_POST_ID']);

                        // Find the user's group_user_id
                        $group_member = $group_obj->getGroupMember($data->user_id, $group_id);
                        $group_user_id = $group_member['GROUP_USER_ID'];

                        // Get the group post ID
                        $group_post_id = $post['GROUP_POST_ID'];

                        // Check post is liked by the user
                        if( $group_post_like->checkPostLiked($group_post_id,$group_user_id ) ){

                            // Append to post
                            $post['USER_LIKED'] = "true";

                        }else{

                            // Append to post
                            $post['USER_LIKED'] = "false";

                        }

                        // Append to the arrays
                        $post['NO_OF_LIKES'] = $stmnt_likes['NO_OF_LIKES'];
                        $post['NO_OF_COMMENTS'] = $stmnt_comments['NO_OF_COMMENTS'];
                        array_push($final_posts_arr,$post);
                    }
                    
                }

            }

            //TODO: Sort the (final_posts_arr) array by date AND IF length > 20 truncate

            // Output posts
            $output["success"]="1";
            $output["message"]=$final_posts_arr;
            echo json_encode($output);


        }else{
            // No groups
            $output["success"]="0";
            $output["message"]="Please join groups to have a feed.";
            echo json_encode($output);
        }

    }else{
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }

?>