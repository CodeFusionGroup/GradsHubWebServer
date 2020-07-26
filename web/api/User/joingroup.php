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
    include_once SITE_ROOT.'/class/group.php';

    $database = new Database();
    $db = $database->getConnection();

    // Create user object
    $user_obj = new User($db);
    // Create group object
    $group_obj = new Group($db);

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // First check if user is already a member of the group
    if( $group_obj->checkGroupMember($data->user_id,$data->group_id) ){
        $output["success"] = "0";
		$output["message"] = "You have already joined this group";
		echo json_encode($output);
    }else{

        // Set the group property value
        $group_obj->id = $data->group_id;

        // Check group visibilty
        if($data->group_visibility == "public"){

            // Make user a group member
            if($group_obj->createGroupMember($data->user_id)){

                $output["success"]="1";
                $output["message"]="Successfully joined group";
                echo json_encode($output);

            }else{
                echo 'User could not be created a member.';
            }

        }else if($data->group_visibility == "private"){

            // Check private group code
            if($group_obj->checkCode($data->group_id,$data->group_code)){

                // Make user a group member
                if($group_obj->createGroupMember($data->user_id) ){

                    $output["success"]="1";
                    $output["message"]="Successfully joined group";
                    echo json_encode($output);

                }else{
                    echo 'User could not be created a member.';
                }
                
            }else{
                $output["success"]="0";
                $output["message"]="Incorrect invite code";
                echo json_encode($output);
            }
        }
    }

?>