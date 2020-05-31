<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// init variables
$post_id_arr = array();
$valid_count = 0;
$output = array();

// Retrieve values
$post_id_string = $_REQUEST["POST_ID"];
$user_id = $_REQUEST["USER_ID"];
$group_id = $_REQUEST["GROUP_ID"];

// Store string post_ids in an array
$post_id_arr = explode(',',$post_id_string);

// Loop through all post_ids
for ( $i = 0;$i < count($post_id_arr); $i++ ){


    // Find out if this user has already liked the post
    $stmnt_check = " SELECT POST_LIKE_ID FROM group_post_like
    WHERE GROUP_USER_ID = (SELECT gu.GROUP_USER_ID FROM group_user AS gu  
    WHERE gu.USER_ID = $user_id  AND gu.GROUP_ID = $group_id) AND GROUP_POST_ID = $post_id_arr[$i] ";

    $result_check = mysqli_query($link,$stmnt_check);

    if( mysqli_num_rows($result_check) > 0){

        $display["success"] = "0";
        $display["message"] = "You have already liked this post.";
        array_push($output,$display);

    }else{

         // Statement to insert like into the database
        $stmnt = "INSERT INTO group_post_like (GROUP_POST_ID,GROUP_USER_ID,POST_LIKE)
        VALUES ( $post_id_arr[$i],(SELECT GROUP_USER_ID FROM GROUP_USER 
        WHERE USER_ID = $user_id  AND GROUP_ID = $group_id) , true)";

        // Insert like into database
        if( $result = mysqli_query($link,$stmnt) ){
            // Increments everytime a post is inserted successfully
            // $valid_count++;

            $display["success"] = "1";
            $display["message"] = "Successfully liked the post.";
            array_push($output,$display);


        }else{
            echo "Error inserting into db";
        }

    }

}

// Output the result and close the link
echo json_encode($output);
mysqli_close($link);

?>