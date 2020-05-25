<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// $stmnt = "INSERT INTO group_post (GROUP_USER_ID, GROUP_ID, POST_TITLE, POST_DATE,POST_ATTACHMENT_URL) 
// VALUES(?,?,?,?,?)";

$stmnt = " INSERT INTO group_post (GROUP_USER_ID,GROUP_ID,POST_TITLE,POST_DATE,POST_ATTACHMENT_URL)
VALUES ( (SELECT GROUP_USER_ID from group_user WHERE USER_ID = ? AND GROUP_ID = ?) ,?,?,?) ";


// $stmnt_url = "INSERT INTO group_post (GROUP_USER_ID,GROUP_ID, POST_TITLE,POST_DATE,POST_ATTACHMENT_URL) 
// VALUES(?,?,?,?,?)";


if( $result = mysqli_prepare($link,$stmnt) ){

    
    mysqli_stmt_bind_param($result,"iisss",$user_id,$group_id,$post_title,$post_date,$post_url);
    $user_id = $_REQUEST["USER_ID"];
    $group_id = $_REQUEST["GROUP_ID"];
    $post_title = $_REQUEST["POST_TITLE"];
    // MYSQL DATE
    $post_date = $_REQUEST["POST_DATE"];
    // $post_date = date("Y-m-d",$temp_date);
    // Encode URL
    $post_url = $_REQUEST["POST_URL"];
    // $temp_url = $_REQUEST["POST_URL"];
    // $post_url = urlencode($temp_url );
    // $post_url = mysqli_real_escape_string($post_url);
    
    // Check if all the values where sent
    if(!isset($user_id ,$group_id,$post_title,$post_date,$post_url)){
        $output["success"]="0";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
        mysqli_close($link);
        die();
    }

    
        // Execute the statement i.e enter record into the table
    if(mysqli_stmt_execute($result)){
        $output["success"]="1";
        $output["message"]="New post created";
        echo json_encode($output);
        mysqli_close($link);

    } else{
        echo "Error executing insert";
    }
        
    
}else{
    echo "Error insert statement";
}
?>