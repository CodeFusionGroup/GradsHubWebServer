<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// First check if user is already a member of the group
if($result = mysqli_prepare($link,"SELECT * FROM group_user WHERE USER_ID = ? AND GROUP_ID = ?")){
    
    mysqli_stmt_bind_param($result,"ii",$user_id,$group_id);
    $user_id = $_REQUEST["USER_ID"];
    $group_id = $_REQUEST["GROUP_ID"];

    mysqli_stmt_execute($result);
    mysqli_stmt_store_result($result);

    // User is a member of the group
    if(mysqli_stmt_num_rows($result) > 0){
        // Unsuccessful
        $output["success"] = "0";
		$output["message"] = "You have already joined this group";
		echo json_encode($output);
        mysqli_close($link);
        
    }else{// User is not a member of the group i.e record doesnt exist in group_user table

        // init group visibility
        $group_visib = $_REQUEST["GROUP_VISIBILITY"];

        // Check if the group the user wants to join is private or public
        if($group_visib == "public"){

            $stmnt = "INSERT INTO group_user(USER_ID,GROUP_ID) VALUES (?,?)";
            if($query = mysqli_prepare($link,$stmnt)){

                mysqli_stmt_bind_param($query,"ii",$user_id,$group_id);
                $user_id = $_REQUEST["USER_ID"];
                $group_id = $_REQUEST["GROUP_ID"];

                mysqli_stmt_execute($query);
                
                // Successful
                $output["success"]="1";
                $output["message"]="Successfully joined group";
                echo json_encode($output);
                mysqli_close($link);
            }

        }else if($group_visib == "private"){  

            // init group invite code
            $group_code = $_REQUEST["GROUP_CODE"]; 
            echo json_encode($group_code); 

            // Check if group code entered is correct
            $stmnt1 = "SELECT GROUP_CODE FROM research_group WHERE GROUP_ID = ?";
            if($request = mysqli_prepare($link,$stmnt1)){

                mysqli_stmt_bind_param($request,"i",$group_id);
                $group_id = $_REQUEST["GROUP_ID"];

                echo json_encode($group_id);

                mysqli_stmt_execute($request);
                mysqli_stmt_store_result($request);

                mysqli_stmt_bind_result($request,$req_groupCode);
                mysqli_stmt_fetch($request);

                echo json_encode($req_groupCode); 

                // Verify group code
                if($req_groupCode == $group_code){

                    $stmnt = "INSERT INTO group_user(USER_ID,GROUP_ID) VALUES (?,?)";
                    if($query = mysqli_prepare($link,$stmnt)){
        
                        mysqli_stmt_bind_param($query,"ii",$user_id,$group_id);
                        $user_id = $_REQUEST["USER_ID"];
                        $group_id = $_REQUEST["GROUP_ID"];
        
                        mysqli_stmt_execute($query);
                        
                        // Successful
                        $output["success"]="1";
                        $output["message"]="Successfully joined group";
                        echo json_encode($output);
                        mysqli_close($link);
                    }

                }else{
                    // Unsuccessful
                    $output["success"]="0";
                    $output["message"]="Incorrect invite code";
                    echo json_encode($output);
                    mysqli_close($link);
                }

            }
            
        }
    }
}
?>