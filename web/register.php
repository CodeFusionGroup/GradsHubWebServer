<?php>

$username = "b6febc76a325a3";
$password = "a4831502";
$database = "heroku_6b7ffb41be0156e";
$host = "us-cdbr-iron-east-04.cleardb.net";
$link = mysqli_connect($host, $username, $password, $database);
//$output=array();



/*if ($result = mysqli_prepare($link,"SELECT USER_EMAIL FROM user WHERE USER_EMAIL=?")){

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
*/
		echo "Email doesnt already exist in the User table!!";
		$test = mysqli_prepare($link,"SELECT USER_EMAIL FROM user WHERE USER_EMAIL=?");
		echo $test;
		if($stmt = mysqli_prepare($link,"INSERT INTO user VALUES(?,?,?,?,?,?)")){
            mysqli_stmt_bind_param($stmt,"ssssis",$user_fname, $user_lname, $hashed_password, $user_email,$user_phone_no, $user_acad_status);
			echo "Insert For loop";

            $user_fname = $_REQUEST["USER_FNAME"];
            $user_lname = $_REQUEST["USER_LNAME"];
			$user_password = $_REQUEST["USER_PASSWORD"];
            $user_email = $_REQUEST["USER_EMAIL"];
            $user_phone_no = $_REQUEST["USER_PHONE_NO"];
            $user_acad_status = $_REQUEST["USER_ACAD_STATUS"];
            //Hash the password
			$hashed_password = password_hash($user_password,PASSWORD_DEFAULT);
			
			
            if(!isset($user_fname, $user_lname, $user_password, $user_email, $user_phone_no, $user_acad_status)){

				echo "You didn't send the required values!";
				$output["success"]="0";
				$output["message"]="You didn't send the required values!";
				echo json_encode($output);
				mysqli_close($link);
				die();

            }
            
            mysqli_stmt_execute($stmt);
            $output["success"]="1";
            $output["message"]="Registration successful!";
            echo json_encode($output);
            mysqli_close($link);

		}else{
			echo "Not binding Insert Statement";
		}
		
// 	}
// }
?>