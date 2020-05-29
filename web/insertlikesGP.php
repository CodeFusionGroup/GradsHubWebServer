<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// init variables
$post_id_arr = array();
$valid_count = 0;
$output = array();

// Retrieve values
$post_id_string = $_REQUEST["POST_ID"];
$user_id = $_REQUEST["USER_ID"];
$group_id = $_REQUEST["GROUP_ID"];

// Store string post_ids in an array
$post_id_arr = explode(',',$post_id_string);

// Loop through all post_ids
for ( $i = 0;$i < count($post_id_arr); $i++ ){


    // Find out if this user has already liked the post
    $stmnt_check = " SELECT POST_LIKE_ID FROM group_post_like
    WHERE GROUP_USER_ID = (SELECT gu.GROUP_USER_ID FROM group_user AS gu  
    WHERE gu.USER_ID = $user_id  AND gu.GROUP_ID = $group_id) AND GROUP_POST_ID = $post_id_arr[$i] ";

    $result_check = mysqli_query($link,$stmnt_check);

    if( mysqli_num_rows($result_check) > 0){

        $display["success"] = "0";
        $display["message"] = "You have already like this post";
        array_push($output,$display);

    }else{

         // Statement to insert like into the database
        $stmnt = "INSERT INTO group_post_like (GROUP_POST_ID,GROUP_USER_ID,POST_LIKE)
        VALUES ( $post_id_arr[$i],(SELECT GROUP_USER_ID AS MemberNumber FROM GROUP_USER 
        WHERE USER_ID = $user_id  AND GROUP_ID = $group_id) , true)";

        // Insert like into database
        if( $result = mysqli_query($link,$stmnt) ){
            // Increments everytime a post is inserted successfully
            // $valid_count++;

            $display["success"] = "0";
            $display["message"] = "You have already like this post";
            array_push($output,$display);


        }else{
            echo "Error inserting into db";
        }

    }

}

// Output the result and close the link
echo json_encode($output);
mysqli_close($link);


// if($valid_count == count($post_id_arr)){
//     $output["success"] = "1";
//     $output["message"] = "Successfully liked posts.";
//     echo json_encode($output);
//     mysqli_close($link);
// }else{
//     echo " Not all likes were inserted";
// }




// ########## ATTEMPTED BINDING&PREPARED STMNTS CODE ##########

// $post_id_string = $_REQUEST["POST_ID"];
// echo "POST_ID string: ";
// echo json_encode($post_id_string);
// // post_id array
// $post_id_arr = array();
// // Store string post_ids in an array
// $post_id_arr = explode(',',$post_id_string);


// // Insert likes for all posts
// for ($i = 0 ; $i < count($post_id_arr); $i++){

//     // Insert likes into database
//     $stmnt = "INSERT INTO group_post_like (GROUP_POST_ID,GROUP_USER_ID,POST_LIKE)
//     VALUES ( ?,(SELECT GROUP_USER_ID AS MemberNumber FROM GROUP_USER 
//     WHERE USER_ID = ? AND GROUP_ID = ?) , true)";

//     if( $result  = mysqli_prepare($link,$stmnt) ){
        
//         echo "POST_ID: ";
//         echo json_encode($post_id_arr[$i]);

//         mysqli_stmt_bind_param($result,"iii",$post_id,$user_id,$group_id);
//         $post_id = $post_id_arr[$i];
//         $user_id = $_REQUEST["USER_ID"];
//         $group_id = $_REQUEST["GROUP_ID"];

//         // Check if all the values were sent
//         if(!isset($post_id, $user_id, $user_id)){
//             // Unsuccessful
//             $output["success"]="0";
//             $output["message"]="You didn't send the required values!";
//             echo json_encode($output);
//             mysqli_close($link);
//             die();

//         }


//         // Execute the query
//         if( mysqli_stmt_execute($result) ){
//             // Successful
//             $output["success"]="1";
//             $output["message"]="Successfully liked the posts";
//             echo json_encode($output);
//             mysqli_close($link);
            
//         }else{
//             echo "Error Inserting";
//         }

        
//     }else{
//         echo "Error with insert statement";
//     }


// }



?>