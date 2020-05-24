<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

$stmnt = "INSERT INTO group_post (GROUP_USER_ID, GROUP_ID, POST_TITLE, POST_DATE,POST_ATTACHMENT_URL) 
VALUES(?,?,?,?,?)";

// $stmnt_url = "INSERT INTO group_post (GROUP_USER_ID,GROUP_ID, POST_TITLE,POST_DATE,POST_ATTACHMENT_URL) 
// VALUES(?,?,?,?,?)";


if( $result = mysqli_prepare($link,$stmnt) ){

    
    mysqli_stmt_bind_param($result,"iisss",$group_userID,$groupID,$post_title,$post_date,$post_url);
    $group_userID = $_REQUEST["GROUP_USER_ID"];
    $groupID = $_REQUEST["GROUP_ID"];
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
    if(!isset($group_userID ,$groupID,$post_title,$post_date)){
        $output["success"]="0";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
        mysqli_close($link);
        die();
    }

    
        // Execute the statement i.e enter record into the table
    if(mysqli_stmt_execute($query)){
        $output["success"]="1";
        $output["message"]="New post created";
        echo json_encode($output);
        mysqli_close($link);

    } else{
        echo "Error Inserting";
    }
        
    
}
?>