<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

$output = array();

if($result = mysqli_prepare($link, "SELECT USER_EMAIL,USER_PASSWORD FROM USER WHERE USER_EMAIL=?")){

	mysqli_stmt_bind_param($result,"s",$user_email);
	$user_email = $_REQUEST["USER_EMAIL"];

	mysqli_stmt_execute($result);
	mysqli_stmt_store_result($result);

	// first check that the user email exists //
	if(mysqli_stmt_num_rows($result) == 0){
		$output["success"] = "-1";
		$output["message"] = "Incorrect email, try again!";
		echo json_encode($output);
		mysqli_close($link);

	} else if(mysqli_stmt_num_rows($result) > 0){ // Email exists proceed to verify password
		mysqli_stmt_bind_param($result,"s",$user_email);
		$user_password = $_REQUEST["USER_PASSWORD"];

		mysqli_stmt_execute($result);
		mysqli_stmt_store_result($result);

		$row = mysqli_fetch_assoc($response);
		if(password_verify($user_password,$row["USER_PASSWORD"])){
			$index["USER_EMAIL"] = $row["USER_EMAIL"];
			array_push($output,$index);
			$output["success"] = "1";
			$output["message"] = "Successfully logged in!";
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
