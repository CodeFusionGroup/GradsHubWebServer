<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the User class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/user.php';

    // Create User object
    $user_obj = new User();

    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    if(isset($data->user_email)){

        // First check if user/email exists
        if($user_obj->checkExists($data->user_email)){

            // ***** Recovery Logic *****

            //The expirary date
            $exp_format = mktime( date("H"), date("i"), date("s"),
            date("m") ,date("d")+1, date("Y") );
            $exp_date = date("Y-m-d H:i:s",$exp_format);
            // Recovery key
            $key = password_hash($data->user_email, PASSWORD_DEFAULT);
            //Get user Fullname
            $stmnt = $user_obj->getUserByEmail($data->user_email);
            $stmnt_res = $stmnt->fetch(PDO::FETCH_ASSOC);
            $fullname = $stmnt_res['USER_FNAME'] ." ".$stmnt_res['USER_LNAME'];
            
            // Insert into Recovery table
            if($user_obj->insertRecovery($data->user_email,$key,$exp_date)){

                // ***** Email Logic ***** 

                if( $user_obj->phpMailer($data->user_email,$key,$fullname) ){

                    
                    $output["success"]="1";
                    $output["message"]="Please check your email to reset password";
                    echo json_encode($output);

                    

                }else{
                    $output["success"]="0";
                    $output["message"]="Email could not be sent";
                    echo json_encode($output);
                }

            }else{
                $output["success"]="0";
                $output["message"]="Error creating recovery";
                echo json_encode($output);
            }

        }else{
            $output["success"]="0";
            $output["message"]="No user registered with this email";
            echo json_encode($output);
        }

    }else{
        //Data is incomplete
        $output["success"]="-1";
        $output["message"]="You didn't send the required values!";
        echo json_encode($output);
    }


?>
