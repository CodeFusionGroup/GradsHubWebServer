<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// Retrieve values
// $user_id = $_REQUEST["USER_ID"];
// $group_id = $_REQUEST["GROUP_ID"];
$post_id = $_REQUEST["POST_ID"];

// Variable
$post_comments = array();

// Statement to retrieve comments
// $stmnt = "SELECT gpc.GROUP_POST_ID FROM group_post_comment AS gpc
// INNER JOIN group_user AS gu ON gpc.GROUP_USER_ID = gu.GROUP_USER_ID
// WHERE gu.USER_ID = $user_id  AND gu.GROUP_ID = $group_id";

$stmnt = "SELECT u.USER_FNAME,u.USER_LNAME,gpc.POST_COMMENT,gpc.POST_COMMENT_DATE FROM group_post_comment AS gpc
INNER JOIN group_user AS gu ON gpc.GROUP_USER_ID = gu.GROUP_USER_ID
INNER JOIN user AS u ON gu.USER_ID = u.USER_ID
WHERE gpc.GROUP_POST_ID = $post_id";

if( $result = mysqli_query($link,$stmnt) ){

    if(mysqli_num_rows($result) > 0){

        // Fetch the comments
        while ($row=$result->fetch_assoc()){
            $comment=$row;
            // push each individual comment into the array
            array_push($post_comments,$comment);
        }

        // Successful
        $output["success"] = "1";
        $output["message"] = $post_comments;
		echo json_encode($output);
		mysqli_close($link);
            
    }else{

        // Unsuccessful
        $output["success"] = "0";
        $output["message"] = "This post has no comments";
        echo json_encode($output);
		mysqli_close($link);

    }

}else{
    echo " Error with executing select statement";
}

?>