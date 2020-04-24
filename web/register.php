<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// Find the user in the database
if ($result = mysqli_prepare($link,"SELECT * FROM user WHERE USER_EMAIL=?")){

	mysqli_stmt_bind_param($result,"s",$user_email);
	$user_email = $_REQUEST["USER_EMAIL"];

	mysqli_stmt_execute($result);
	mysqli_stmt_store_result($result);

    // Find out if the user email doesnt already exist in the User table
	if(mysqli_stmt_num_rows($result) > 0){
		$output["success"]="-1";
		$output["message"]="Email already exists!, Please use another email.";
		echo json_encode($output);
		mysqli_close($link);
	}else{ // Email doesnt already exist in the User table

		// Insert the user into the database
		$stmnt = "INSERT INTO user (USER_FNAME,USER_LNAME,USER_PASSWORD,USER_EMAIL,USER_PHONE_NO,USER_ACAD_STATUS) 
		VALUES(?,?,?,?,?,?)";
		if($query = mysqli_prepare($link,$stmnt)){
			
            mysqli_stmt_bind_param($query,"ssssss",$user_fname, $user_lname, $hashed_password, $user_email,$user_phone_no, $user_acad_status);

            $user_fname = $_REQUEST["USER_FNAME"];
            $user_lname = $_REQUEST["USER_LNAME"];
			$user_password = $_REQUEST["USER_PASSWORD"];
            $user_email = $_REQUEST["USER_EMAIL"];
            $user_phone_no = $_REQUEST["USER_PHONE_NO"];
			$user_acad_status = $_REQUEST["USER_ACAD_STATUS"];
			
            //Hash the password
			$hashed_password = password_hash($user_password,PASSWORD_DEFAULT);
			
			// Check if all the values were sent
            if(!isset($user_fname, $user_lname, $user_password, $user_email, $user_phone_no, $user_acad_status)){
				// Unsuccessful
				$output["success"]="0";
				$output["message"]="You didn't send the required values!";
				echo json_encode($output);
				mysqli_close($link);
				die();

            }
			
			// Execute the query
			mysqli_stmt_execute($query);
			
			// Successful
            $output["success"]="1";
            $output["message"]="Registration successful!";
            echo json_encode($output);
            mysqli_close($link);

		}
 	}
 }
?>