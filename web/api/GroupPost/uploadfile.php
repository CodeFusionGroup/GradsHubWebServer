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

    // Uploaded file data
    $file= $_FILES['file']['name'];
    $temp_name= $_FILES['file']['tmp_name'];
    // $error= $_FILES['file']['error'];
    // $type= $_FILES['file']['type'];
    // $size= $_FILES['file']['size'];

    // UploadFiles directory
    $curr_server = $_SERVER["DOCUMENT_ROOT"];
    $path = "";
    $directoryName = "";
    if($curr_server == "\/app\/web"){
        // $path = "https://gradshub.herokuapp.com/uploadedFiles/".$file;
        $path = "https://gradshub.herokuapp.com/uploadedFiles/";
        $directoryName = "https://gradshub.herokuapp.com/uploadedFiles/";
    }else{
        // $path = SITE_ROOT."/uploadedFiles/".$file;
        $path = SITE_ROOT."/uploadedFiles/";
        $directoryName = SITE_ROOT."/uploadedFiles/";
    }
    
    
    //Check if the directory already exists.
    if(!is_dir($directoryName)){
        //Directory does not exist, so lets create it.
        mkdir($directoryName, 0755);
    }

    // Check errors
    switch($_FILES['file']['error']){
        case UPLOAD_ERR_OK:
            // TODO: Ensure that file is really secure https://www.php.net/manual/en/features.file-upload.php
            // https://www.php.net/manual/en/function.move-uploaded-file.php

            // Check MIME Type(add more mime types for images)
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            if(false === $ext = array_search(
                $finfo->file($_FILES['file']['tmp_name']),
                array('pdf'=>'application/pdf'),true
            )){
                $output["success"] = "-1";
                $output["message"] = "File uploaded is too big";
                echo json_encode($output);
            }else{

                // Hash the file (obtain safe unique name from its binary data)
                $hashed_path = sprintf($path.'%s.%s',sha1_file($temp_name),$ext);
                // Upload the file
                $res_upload = move_uploaded_file($temp_name,$hashed_path );
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
            
                    // Put path to file in database
                    if($group_post_obj->uploadPDF()){
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
            break;
        case UPLOAD_ERR_INI_SIZE:
            $output["success"] = "-1";
            $output["message"] = "File uploaded is too big";
            echo json_encode($output);
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $output["success"] = "-1";
            $output["message"] = "Exceeded filesize limit";
            echo json_encode($output);
            break;
        case UPLOAD_ERR_NO_FILE:
            $output["success"] = "-1";
            $output["message"] = "No file was uploaded";
            echo json_encode($output);
            break;
    }
  }

?>