<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the classes
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/user.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/log.php';

    // Create User object
    $user_obj = new User();

    // Create Log object
    $log_obj = new Log();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    $stmnt = $user_obj->getUserByEmail($data->email);
    $user_count = $stmnt->rowCount();

    // Check if user email exists
    if($user_count > 0){
        
        $dataRow = $stmnt->fetch(PDO::FETCH_ASSOC);

        // Check password
        $user_password = $data->password;
        $hashed_password = $dataRow['USER_PASSWORD'];
        if(password_verify($user_password,$hashed_password)){
            // Put user details into a JSON object
            $res_user["USER_EMAIL"] = $dataRow['USER_EMAIL'];
			$res_user["USER_ID"] = $dataRow['USER_ID'];
			$res_user["USER_FNAME"] = $dataRow['USER_FNAME'];
			$res_user["USER_LNAME"] = $dataRow['USER_LNAME'];
			$res_user["USER_PHONE_NO"] = $dataRow['USER_PHONE_NO'];
			$res_user["USER_ACAD_STATUS"] = $dataRow['USER_ACAD_STATUS'];
			$res_user["USER_PROFILE_PICTURE"] = $dataRow['USER_PROFILE_PICTURE'];
			$res_user["USER_NAME"] = $dataRow['USER_NAME'];
			

            // Output
			$output["success"] = "1";
            $output["message"] = "Successfully logged in";
            $output["user"] = $res_user;
            echo json_encode($output);

            // Log the sign in
            $log_msg = " User: ". $data->email . ", has successfully logged in";
            $log_obj->infoLog($log_msg);
        }else{

            // Unsuccessful
			$output["success"] = "0";
			$output["message"] = "Incorrect password. Please try again!";
            echo json_encode($output);

            // Log the failed sign in
            $log_msg = " User: ". $data->email . ", has tried an incorrect password";
            $log_obj->infoLog($log_msg);
        }

    }else{
        $output["success"] = "-1";
		$output["message"] = "Email doesn't exist, please try again";
        echo json_encode($output);
        
        // Log the incorrect login
        $log_msg = " Email: " . $data->email . " does not exist.";
        $log_obj->infoLog($log_msg);
    }

?>
