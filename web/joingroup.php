<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);


// First check if user is already a member of the group
if($result = mysqli_prepare($link,"SELECT * FROM group_user WHERE USER_ID = ? AND RESEARCH_GROUP_ID = ?")){
    
    mysqli_stmt_bind_param($result,"ii",$user_id,$group_id);
    $user_id = $_REQUEST["USER_ID"];
    $group_id = $_REQUEST["RESEARCH_GROUP_ID"];

    mysqli_stmt_execute($result);
    mysqli_stmt_store_result($result);

    mysqli_stmt_bind_result($result,$res_userID,$res_groupID);
    mysqli_stmt_fetch($result);

    // User is a member of the group
    if(mysqli_stmt_num_rows($result) > 0){
        // Unsuccessful
        $output["success"] = "0";
		$output["message"] = "You have already joined this group";
		echo json_encode($output);
        mysqli_close($link);
        
    }else{// User is not a member of the group

        echo json_encode($res_userID);
        echo json_encode($res_groupID);
        $stmnt = "INSERT INTO group_user(USER_ID,RESEARCH_GROUP_ID) VALUES ($res_userID,$res_groupID)";
        if($query = mysqli_query($link,$stmnt)){

            // Execute the statement i.e enter record into the table
            mysqli_stmt_execute($query);
            // Successful
            $output["success"]="1";
            $output["message"]="Successfully joined group";
            echo json_encode($output);
            mysqli_close($link);
        }
    }
}

?>