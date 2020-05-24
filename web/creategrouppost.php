<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

$stmnt = "INSERT INTO group_post (GROUP_USER_ID, GROUP_ID, POST_TITLE, POST_DATE, POST_ATTACHMENT_FILE,POST_ATTACHMENT_URL) 
VALUES(?,?,?,?,?)";

// $stmnt_url = "INSERT INTO group_post (GROUP_USER_ID,GROUP_ID, POST_TITLE,POST_DATE,POST_ATTACHMENT_URL) 
// VALUES(?,?,?,?,?)";


// If choice was a url
if( $query = mysqli_prepape($link,$stmnt) ){

    mysqli_stmt_bind_param($query,"iissss",$group_userID,$groupID,$post_title,$post_date,$post_file,$post_url);
    $group_userID = $_REQUEST["GROUP_USER_ID"];
    $groupID = $_REQUEST["GROUP_ID"];
    $post_title = $_REQUEST["POST_TITLE"];
    $post_date = "current_date()";
    $post_file = $_REQUEST["POST_FILE"];
    $post_url = $_REQUEST["POST_URL"];
    

    // Check if all the values where sent
    if(!isset($group_userID ,$groupID,$post_title,$post_date,$post_url )){
        $output["success"]="0";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
        mysqli_close($link);
        die();
    }

    // Check if attachment is url or pdf
    if($post_file == ""){ // Attachment == URL

        // Encode url
        $post_url = urlencode($post_url);
        $post_url = mysql_real_escape_string($post_url);

        // Execute the statement i.e enter record into the table
        mysqli_stmt_execute($query);

    }else if( $post_url == ""){ // Attachment == file
        // Implement file code
    }

    $output["success"]="1";
    $output["message"]="New post created";
    echo json_encode($output);
    mysqli_close($link);
    

}


?>