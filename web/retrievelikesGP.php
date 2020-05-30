<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// Retrieve values
$user_id = $_REQUEST["USER_ID"];
$group_id = $_REQUEST["GROUP_ID"];

// Variable
$post_id_arr = array();

// Statement to retrieve likes
$stmnt = "SELECT gpl.GROUP_POST_ID FROM group_post_like AS gpl
INNER JOIN group_user AS gu ON gpl.GROUP_USER_ID = gu.GROUP_USER_ID
WHERE gu.USER_ID = $user_id AND gu.GROUP_ID = $group_id";

if( $result = mysqli_query($link,$stmnt) ){

    if(mysqli_num_rows($result) > 0){

        // Fetch the post_ids
        while ($row=$result->fetch_assoc()){
            $post_id=$row;
            // push each individual id into the array
            array_push($post_id_arr,$post_id);
        }

        // Successful
        $output["success"] = "1";
        $output["message"] = $post_id_arr;
		echo json_encode($output);
		mysqli_close($link);
            
    }else{

        // Unsuccessful
        $output["success"] = "0";
        $output["message"] = "You have not liked any posts.";
        echo json_encode($output);
		mysqli_close($link);

    }

}else{
    echo " Error with executing select statement";
}

?>