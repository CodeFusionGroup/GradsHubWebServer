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
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/group.php';

    // Create group object
    $group_obj = new Group();
    // Create user object
    $user_obj = new User();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    // Make sure data is not empty
    if(isset($data->name,$data->visibility,$data->email)){

        // Check if group name is already taken
        $group_query = $group_obj->getGroupByName($data->name);
        $count_group_query = $group_query->rowCount();

        if($count_group_query == 0){

            // Set the group property values
            $group_obj->name = $data->name;
            $group_obj->visibility = $data->visibility;
            $group_obj->code = $data->code;

            // Create the group
            if($group_obj->createGroup()){

                // Get the id of the new group
                $new_group_query = $group_obj->getGroupByName($group_obj->name);
                $new_group_row = $new_group_query->fetch(PDO::FETCH_ASSOC);
                $group_obj->id = $new_group_row["GROUP_ID"];

                // Insert user as admin of the group
                $user_query = $user_obj->getUserByEmail($data->email);
                $user_row = $user_query->fetch(PDO::FETCH_ASSOC);
                $user_id = $user_row["USER_ID"];

                $success = 0;

                if($group_obj->createGroupAdmin($user_id)){
                    $success=$success+1;
                }else{
                    // Debugging purposes
                    echo 'Group admin could not be created.';
                }

                if($group_obj->createGroupMember($user_id)){
                    $success=$success+1;
                }else{
                    // Debugging purposes
                    echo 'Group member could not be created.';
                }

                if($success == 2){
                    $output["success"]="1";
                    $output["message"]="New group created";
                    echo json_encode($output);
                }else{
                    echo 'Inserting admin or member did not work';
                }
                
            } else{
                echo 'Group could not be created.';
            }
        }else{
            $output["success"]="-1";
            $output["message"]="Group name is taken. Please choose another.";
		    echo json_encode($output);
        }


    }else{
        $output["success"]="0";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }
?>