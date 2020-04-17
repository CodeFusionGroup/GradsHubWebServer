<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// init some vars
$output=array();
$display=array();

// Find the user_id
if($query = mysqli_prepare($link,"SELECT USER_ID FROM USER WHERE USER_EMAIL = ? ")){

    mysqli_stmt_bind_param($query,"s",$user_email);
    $user_email = $_REQUEST["USER_EMAIL"];

    mysqli_stmt_execute($query);
    mysqli_stmt_store_result($query);

    mysqli_stmt_bind_result($query,$query_user_id);
    mysqli_stmt_fetch($query);
    
    // first check that the user email is correct //
	if(mysqli_stmt_num_rows($query) == 0){
		$output["success"] = "-1";
		$output["message"] = "Email doesn't exist, please try again";
		echo json_encode($output);
		mysqli_close($link);

    } else if(mysqli_stmt_num_rows($query) > 0){ // Email is correct proceed to find the groups
        // $stmnt = "SELECT rg.RESEARCH_GROUP_ID, rg.GROUP_NAME,rg.GROUP_VISIBILITY, rg.GROUP_CODE FROM research_group rg
        // INNER JOIN group_user gu ON rg.RESEARCH_GROUP_ID = gu.RESEARCH_GROUP_ID
        // WHERE gu.USER_ID = $query_user_id";

        // $stmnt_2 = "SELECT u.USER_EMAIL FROM research_group rg
        // INNER JOIN group_admin ga ON rg.RESEARCH_GROUP_ID = ga.RESEARCH_GROUP_ID
        // INNER JOIN user u ON ga.USER_ID = u.USER_ID
        // WHERE u.USER_ID = $query_user_id";

        $stmnt = "SELECT rg.RESEARCH_GROUP_ID, rg.GROUP_NAME,rg.GROUP_VISIBILITY, rg.GROUP_CODE, u.USER_EMAIL FROM group_user gu
        INNER JOIN research_group rg ON gu.RESEARCH_GROUP_ID = rg.RESEARCH_GROUP_ID
        INNER JOIN group_admin ga ON rg.RESEARCH_GROUP_ID = ga.RESEARCH_GROUP_ID
        INNER JOIN user u ON ga.USER_ID = u.USER_ID
        WHERE gu.USER_ID = $query_user_id";

        if($result = mysqli_query($link,$stmnt)){
            
            // User belongs to one group or more
            if(mysqli_num_rows($result) > 0){

                while ($row=$result->fetch_assoc()){
                    $output[]=$row;
                }
                echo json_encode($output);
                mysqli_close($link);
            }else{
                $temp["success"]="0";
                array_push($display,$temp);
                $temp2["message"]="You have not joined any groups";
                array_push($display,$temp2);
                echo json_encode($display);
                mysqli_close($link);
            }


        }

    }

}


?>