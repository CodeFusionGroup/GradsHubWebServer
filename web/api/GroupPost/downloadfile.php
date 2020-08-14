/*<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Retrieve all user PDFS for a post
    $stmnt = $group_post_OBJ->downloadFile($data->user_id,$data->group_id);

    //$group_post_obj->group_post_id = $data->post_id;



// Downloads files

    // fetch file to download from database


    //$sql = "SELECT * FROM group_post  WHERE group_post_id=$group_post_obj->group_pst_id;
    //$result = mysqli_query($conn, $stmnt);


    $file = mysqli_fetch_assoc($stmnt);
    $filepath = "/uploads/" . $file['name'];

    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filepath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize("/uploads/" . $file['name']));
        readfile("/uploads/" . $file['name']);

        
    }



?>*/
