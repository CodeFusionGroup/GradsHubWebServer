<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

$stmnt = "INSERT INTO group_post_like (GROUP_POST_ID,GROUP_USER_ID,POST_LIKE)
VALUES ( ?,(SELECT GROUP_USER_ID AS MemberNumber FROM GROUP_USER 
WHERE USER_ID = ? AND GROUP_ID = ?) , true)";

if( $result  = mysqli_prepare($link,$stmnt)){

    mysqli_stmt_bind_param($result,"iii",$post_id,$user_id,$group_id);
    $post_id = $_REQUEST["POST_ID"];
    $user_id = $_REQUEST["USER_ID"];
    $user_id = $_REQUEST["GROUP_ID"];


    // Check if all the values were sent
    if(!isset($post_id, $user_id, $user_id)){
        // Unsuccessful
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
        mysqli_close($link);
        die();

    }

    // Execute the query
    if( mysqli_stmt_execute($query) ){
        // Successful
        $output["success"]="1";
        $output["message"]="Registration successful!";
        echo json_encode($output);
        mysqli_close($link);
    }else{
        echo "Error Inserting";
    }

    
}else{
    echo "Error with insert statement";
}



?>