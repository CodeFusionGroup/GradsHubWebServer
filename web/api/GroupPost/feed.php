<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the Group and Group Post class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group_post.php';

    // Create Group Post object
    $group_post_obj = new GroupPost();
 

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));


    // Make the database queries
    $stmnt_likes_comments = $group_post_obj->readCommentsAndLikes(); 
    $stmnt_posts = $group_post_obj->feed($data->user_id); 

    // Counts
    $stmnt_posts_counts = $stmnt_posts->rowCount();

    // Check if posts exist in the group
    if($stmnt_posts_counts>0){

        $post_counts_arr = array();
        $full_post_arr = array();

        // Fetch the post info
        while ( $row = $stmnt_posts->fetch(PDO::FETCH_ASSOC) ){
            extract($row);
            // Push info into an array
            array_push($full_post_arr,$row);
        }

        // Fetch the no of likes and comments
        while ( $row=$stmnt_likes_comments->fetch(PDO::FETCH_ASSOC) ){
            extract($row);
            // Push counts into an array
            array_push($post_counts_arr,$row);
        }

        // Combine everything into one array
        for($i = 0 ;$i < count($full_post_arr); $i++ ){

            // $full_post_arr[$i]["NO_OF_COMMENTS"] = $post_counts_arr[$i]["NO_OF_COMMENTS"];
            // $full_post_arr[$i]["NO_OF_LIKES"] = $post_counts_arr[$i]["NO_OF_LIKES"];
            
            $full_post_arr[$i]["NO_OF_COMMENTS"] = isset($post_counts_arr[$i]["NO_OF_COMMENTS"]) ? $post_counts_arr[$i]["NO_OF_COMMENTS"]: null;
            $full_post_arr[$i]["NO_OF_LIKES"] = isset($post_counts_arr[$i]["NO_OF_LIKES"]) ? $post_counts_arr[$i]["NO_OF_LIKES"]:null;


        }

        $output["success"] = "1";
        $output["message"] = $full_post_arr;
        echo json_encode($output);

    }else{
        $output["success"] = "0";
        $output["message"] = "Feed Empty.";
        echo json_encode($output);
    }

?>