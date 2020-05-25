<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// $stmnt = "INSERT INTO group_post (GROUP_USER_ID, GROUP_ID, POST_TITLE, POST_DATE,POST_ATTACHMENT_URL) 
// VALUES(?,?,?,?,?)";

// $stmnt_2 = " INSERT INTO group_post (GROUP_USER_ID,GROUP_ID,POST_TITLE,POST_DATE,POST_ATTACHMENT_URL)
// VALUES ( (SELECT GROUP_USER_ID from group_user WHERE USER_ID = ? AND GROUP_ID = ?) ,?,?,?) ";

$stmnt = "SELECT GROUP_USER_ID from group_user WHERE USER_ID = ? AND GROUP_ID = ?";

// $stmnt_url = "INSERT INTO group_post (GROUP_USER_ID,GROUP_ID, POST_TITLE,POST_DATE,POST_ATTACHMENT_URL) 
// VALUES(?,?,?,?,?)";

if( $query = mysqli_prepare($link,$stmnt) ){

    mysqli_stmt_bind_param($query,"ii",$user_id,$group_id);
    $user_id = $_REQUEST["USER_ID"];
    $group_id = $_REQUEST["GROUP_ID"];

    mysqli_stmt_execute($query);
    mysqli_stmt_store_result($query);

    mysqli_stmt_bind_result($query,$res_group_userID);
    mysqli_stmt_fetch($query);

    // The user is part of this group
    if(mysqli_stmt_num_rows($query) > 0){

        $stmnt_2 = " INSERT INTO group_post (GROUP_USER_ID,GROUP_ID,POST_TITLE,POST_DATE,POST_ATTACHMENT_URL)
        VALUES ( $res_group_userID,?,?,?,?) ";

        if( $query_2 = mysqli_prepare($link,$stmnt_2) ){

            mysqli_stmt_bind_param($query_2,"isss",$group_id,$post_title,$post_date,$post_url);
            $group_id = $_REQUEST["GROUP_ID"];
            $post_title = $_REQUEST["POST_TITLE"];
            $post_date = $_REQUEST["POST_DATE"];
            $post_url = $_REQUEST["POST_URL"];

            // Check if all the values where sent
            if(!isset($group_id,$post_title,$post_date,$post_url)){
                $output["success"]="0";
                $output["message"]="You didn't send the required values!";
                echo json_encode($output);
                mysqli_close($link);
                die();
            }

            // Execute the statement i.e enter record into the table
            if(mysqli_stmt_execute($query_2)){
                $output["success"]="1";
                $output["message"]="New post created";
                echo json_encode($output);
                mysqli_close($link);

            } else {
                echo "Error executing insert";
            }


        }else{
            echo "Error with the insert statement";
        }

    }


}
// if( $result_2 = mysqli_prepare($link,$stmnt) ){

    
//     mysqli_stmt_bind_param($result,"iisss",$user_id,$group_id,$post_title,$post_date,$post_url);
//     $user_id = $_REQUEST["USER_ID"];
//     $group_id = $_REQUEST["GROUP_ID"];
//     $post_title = $_REQUEST["POST_TITLE"];
//     // MYSQL DATE
//     $post_date = $_REQUEST["POST_DATE"];
//     // $post_date = date("Y-m-d",$temp_date);
//     // Encode URL
//     $post_url = $_REQUEST["POST_URL"];
//     // $temp_url = $_REQUEST["POST_URL"];
//     // $post_url = urlencode($temp_url );
//     // $post_url = mysqli_real_escape_string($post_url);
    
//     // Check if all the values where sent
//     if(!isset($user_id ,$group_id,$post_title,$post_date,$post_url)){
//         $output["success"]="0";
//         $output["message"]="You didn't send the required values!";
//         echo json_encode($output);
//         mysqli_close($link);
//         die();
//     }

    
//         // Execute the statement i.e enter record into the table
//     if(mysqli_stmt_execute($result)){
//         $output["success"]="1";
//         $output["message"]="New post created";
//         echo json_encode($output);
//         mysqli_close($link);

//     } else{
//         echo "Error executing insert";
//     }
        
    
// }else{
//     echo "Error with the insert statement";
// }
?>