<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// Check if group name is already taken
if($result = mysqli_prepare($link,"SELECT GROUP_NAME FROM research_group WHERE GROUP_NAME=?")){

    mysqli_stmt_bind_param($result,"s",$group_name);
    $group_name = $_REQUEST["GROUP_NAME"];
    
    mysqli_stmt_execute($result);
    mysqli_stmt_store_result($result);
    
    // Group name exists in the db
    if(mysqli_stmt_num_rows($result) > 0){
        $output["success"]="-1";
		$output["message"]="Group name is taken. Please choose another.";
		echo json_encode($output);
		mysqli_close($link);
    }else{// Group name doesnt exist in the db

        echo "Group name doesnt exist in the db";
        echo json_encode(mysqli_prepare($link,"INSERT INTO research_group (GROUP_NAME,GROUP_VISIBILITY, GROUP_CODE) VALUES(?,?,?)"));
        // Insert new group into database
        if($stmt = mysqli_prepare($link,"INSERT INTO research_group (GROUP_NAME,GROUP_VISIBILITY, GROUP_CODE) VALUES(?,?,?)")){

            mysqli_stmt_bind_param($stmt,"sss",$group_name,$group_visibility,$group_code);
            $group_name = $_REQUEST["GROUP_NAME"];
            $group_visibility = $_REQUEST["GROUP_VISIBILTY"];
            $group_code = $_REQUEST["GROUP_CODE"];

            echo "Insert new group into database";
            // Check if all the values where sent
            if(!isset($group_name ,$group_visibility,$group_code )){
                $output["success"]="0";
		        $output["message"]="You didn't send the required values!";
				echo json_encode($output);
				mysqli_close($link);
				die();
            }

            // Execute the statement i.e enter record into the table
            mysqli_stmt_execute($stmt);

            // Insert User as admin of the new group
            if($stmt2 = mysqli_prepare($link,"INSERT INTO group_admin (USER_ID,RESEARCH_GROUP_ID) VALUES ((SELECT USER_ID FROM user WHERE USER_EMAIL = ?),
            (SELECT RESEARCH_GROUP_ID FROM research_group WHERE GROUP_NAME = ?))")){

                mysqli_stmt_bind_param($stmt2,"ss",$user_email,$group_name);
                $user_email = $_REQUEST["USER_EMAIL"];
                $group_name = $_REQUEST["GROUP_NAME"];

                // Execute the statement i.e enter record into the table
                mysqli_stmt_execute($stmt2);

            }

            // Insert User as a member of the new group
            if($stmt3 = mysqli_prepare($link,"INSERT INTO group_user (USER_ID,RESEARCH_GROUP_ID) VALUES ((SELECT USER_ID FROM user WHERE USER_EMAIL = ?),
            (SELECT RESEARCH_GROUP_ID FROM research_group WHERE GROUP_NAME = ?))")){

                mysqli_stmt_bind_param($stmt3,"ss",$user_email,$group_name);
                $user_email = $_REQUEST["USER_EMAIL"];
                $group_name = $_REQUEST["GROUP_NAME"];

                // Execute the statement i.e enter record into the table
                mysqli_stmt_execute($stmt3);
            }

            $output["success"]="1";
            $output["message"]="New group created";
            echo json_encode($output);
            mysqli_close($link);
        }

        

        


    }

}







?>