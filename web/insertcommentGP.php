<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// Retrieve values
$post_id = $_REQUEST["POST_ID"];
$user_id = $_REQUEST["USER_ID"];
$group_id = $_REQUEST["GROUP_ID"];


// Statement to insert comment into the database
$stmnt = "INSERT INTO group_post_comment (GROUP_POST_ID,GROUP_USER_ID,POST_COMMENT,POST_COMMENT_DATE)
VALUES ( $post_id ,(SELECT GROUP_USER_ID FROM GROUP_USER 
WHERE USER_ID = $user_id  AND GROUP_ID = $group_id) ,?,?)";

if($query = mysqli_prepare($link,$stmnt)){

    mysqli_stmt_bind_param($query,"ss",$post_comment,$post_date);
    $post_comment = $_REQUEST["POST_COMMENT"];
    $post_date = $_REQUEST["POST_COMMENT_DATE"];

    // Check if all the values where sent
    if(!isset($post_id ,$user_id ,$group_id )){
        // Error
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
        mysqli_close($link);
        die();
    }

    // Execute the statement i.e enter record into the table
    mysqli_stmt_execute($query);

    // Success
    $output["success"]="1";
    $output["message"]="New comment created";
    echo json_encode($output);
    mysqli_close($link);

}
?>