<?php
 if($_SERVER['REQUEST_METHOD']=='POST'){
      
	//including the database connection file
    require_once __DIR__."/../../config.php";

    include_once SITE_ROOT.'/config/database.php';
    include_once SITE_ROOT.'/class/group.php';
    include_once SITE_ROOT.'/class/group_post.php';

    $database = new Database();
    $db = $database->getConnection();

    // Create user object
    $group_post_obj = new GroupPost($db);
    // Create group object
    $group_obj = new Group($db);

  	  	
    $file= $_FILES['file']['name'];
    $temp_name= $_FILES['file']['tmp_name'];
    $error= $_FILES['file']['error'];
    $type= $_FILES['file']['type'];
    $size= $_FILES['file']['size'];
    $path = SITE_ROOT."/uploadedFiles/".$file;

    // Check errors : https://www.php.net/manual/en/features.file-upload.errors.php
    if( $error == '1'){
        $output["success"] = "-1";
        $output["message"] = "File uploaded is too big";
        echo json_encode($output);
    }else{

        // TODO: Ensure that file is really secure https://www.php.net/manual/en/features.file-upload.php
        // https://www.php.net/manual/en/function.move-uploaded-file.php

        $res_upload = move_uploaded_file($temp_name,$path);
        if($res_upload){

            // Retrieve form-data
            $post_title = $_POST["post_title"];
            $user_id = $_POST["user_id"];
            $group_id = $_POST["group_id"];
            $post_date = $_POST["post_date"];
    
            // retrieve group member
            $group_member = $group_obj->getGroupMember($user_id,$group_id);
    
            // Set the group property values
            $group_post_obj->group_user_id = $group_member["GROUP_USER_ID"];
            $group_post_obj->group_id = $group_id;
            $group_post_obj->title = $post_title;
            $group_post_obj->date = $post_date;
            $group_post_obj->attachment_file = $path;
    
            if($group_post_obj->uploadPDF()){
            
                // $query= "SELECT * FROM upload_image_video WHERE pathToFile='$url'";
                // $result= mysqli_query($con, $query);
                // $emparray = array();
                //     if(mysqli_num_rows($result) > 0){  
                //         while ($row = mysqli_fetch_assoc($result)) {
                //             $emparray[] = $row;
                //         }
                //     echo json_encode(array( "status" => "true","message" => "Successfully file added!" , "data" => $emparray) );
                //     }else{
                //         echo json_encode(array( "status" => "false","message" => "Failed!") );
                //     }
                $output["success"] = "1";
                $output["message"] = "successfully uploaded file";
                echo json_encode($output);
            }else{
                // Debugging purposes
                echo json_encode(array( "status" => "false","message" => "Failed to upload to database") );
            }
        }else{
            // Debugging purposes
            echo json_encode(array( "status" => "false","message" => "Failing to move file!") );
        }
    }
  }

?>