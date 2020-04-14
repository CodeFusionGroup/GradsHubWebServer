<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

//$output = array();
$success =  array();

if($result = mysqli_prepare($link, "SELECT USER_EMAIL,USER_PASSWORD,USER_ID,USER_FNAME,USER_LNAME,USER_PHONE_NO,USER_ACAD_STATUS 
	FROM user WHERE USER_EMAIL=?")){

	mysqli_stmt_bind_param($result,"s",$user_email);
	$user_email = $_REQUEST["USER_EMAIL"];

	mysqli_stmt_execute($result);
	mysqli_stmt_store_result($result);

	mysqli_stmt_bind_result($result,$res_email,$hashed_password,$res_userid,$res_fname,$res_lname,$res_phoneno,$res_acadstat);
	mysqli_stmt_fetch($result);

	// first check that the user email exists //
	if(mysqli_stmt_num_rows($result) == 0){
		$output["success"] = "-1";
		$output["message"] = "Email doesn't exist, please try again";
		echo json_encode($output);
		mysqli_close($link);

	} else if(mysqli_stmt_num_rows($result) > 0){ // Email exists proceed to verify password
		$user_password = $_REQUEST["USER_PASSWORD"];

		// Verify hashed password
		if( password_verify($user_password,$hashed_password) ){

			$message["USER_EMAIL"] = $res_email;
			$message["USER_ID"] = $res_userid;
			$message["USER_FNAME"] = $res_fname;
			$message["USER_LNAME"] = $res_lname;
			$message["USER_PHONE_NO"] = $res_phoneno;
			$message["USER_ACAD_STATUS"] = $res_acadstat;
			$output["success"] = "1";
			$output["message"] = $message;
			echo json_encode($output);
			mysqli_close($link);

		}else{
			$output["success"] = "0";
			$output["message"] = "Incorrect password. Please try again!";
			echo json_encode($output);
			mysqli_close($link);
		}

	}

}
?>
