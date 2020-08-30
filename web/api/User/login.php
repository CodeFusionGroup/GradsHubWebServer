<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for file url
    require_once __DIR__."/../../config.php";

    include_once SITE_ROOT.'/config/database.php';
    include_once SITE_ROOT.'/class/user.php';

    $database = new Database();
    $db = $database->getConnection();

    // Create user object
    $user_obj = new User($db);

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

            // Output
			$output["success"] = "1";
            $output["message"] = "Successfully logged in";
            $output["user"] = $res_user;
            echo json_encode($output);
        }else{
            // Unsuccessful
			$output["success"] = "0";
			$output["message"] = "Incorrect password. Please try again!";
			echo json_encode($output);
        }

    }else{
        $output["success"] = "-1";
		$output["message"] = "Email doesn't exist, please try again";
		echo json_encode($output);
    }

?>