<?php
$username = "s1733164";
$password = "CNSPass3304bm";
$database = "d1733164";
$link = mysqli_connect("127.0.0.1", $username, $password, $database);
$output = array();

$user_email = $_REQUEST["USER_EMAIL"];
$user_password = $_REQUEST["USER_PASSWORD"];

if(!isset($user_email,$user_password)){
	$output["result"]="You didn't send the required values";
        echo json_encode($output);
	die();
}

$response = mysqli_query($link, "SELECT USER_EMAIL,USER_PASSWORD FROM USER WHERE USER_EMAIL='$user_email'");
$output = array();

if(mysqli_num_rows($response)== 0){

	$output["success"] = "0";
        $output["message"] = "incorrect email, try again!";
        echo json_encode($output);
         mysqli_close($link);


}else if(mysqli_num_rows($response) > 0){

	$row = mysqli_fetch_assoc($response);
	if($lecturer_password==$row["USER_PASSWORD"]){

		// $index["USER_EMAIL"] = $row["USER_EMAIL"];
		// array_push($output,$index);
		$output["success"] = "1";
		$output["message"] = "successfully logged in!";
		echo json_encode($output);
		mysqli_close($link);

	}else{
		$output["success"] = "0";
		$output["message"] = "incorrect password, try again!";
		echo json_encode($output);
		mysqli_close($link);
	    }
}
?>
