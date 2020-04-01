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

    } else if(mysqli_stmt_num_rows($query) > 0){ // Email is correct proceed to find group names
        $stmnt = "SELECT research_group.GROUP_NAME FROM research_group INNER JOIN group_user 
        ON research_group.RESEARCH_GROUP_ID = group_user.GROUP_USER_ID WHERE USER_ID = $query_user_id";

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