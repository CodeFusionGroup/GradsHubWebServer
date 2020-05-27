<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);


// $stmnt = "SELECT POST_TITLE,POST_DATE,POST_ATTACHMENT_URL FROM group_post where GROUP_ID = ?";

$stmnt = " SELECT u.USER_FNAME, u.USER_LNAME, gp.GROUP_POST_ID,gp.POST_TITLE, gp.POST_DATE,gp.POST_ATTACHMENT_URL FROM group_post as gp 
INNER JOIN group_user as gu ON gp.GROUP_USER_ID = gu.GROUP_USER_ID
INNER JOIN user as u ON gu.USER_ID = u.USER_ID
where gp.GROUP_ID = ? ";



if ( $result = mysqli_prepare($link,$stmnt)){

    mysqli_stmt_bind_param($result,"i",$group_id);
    $group_id = $_REQUEST["GROUP_ID"];

    mysqli_stmt_execute($result);
    mysqli_stmt_store_result($result);

    mysqli_stmt_bind_result($result,$res_userFname,$res_userLname,$res_groupPostID,$res_postTitle,$res_postDate,$res_postUrl);
    mysqli_stmt_fetch($result);


    if(mysqli_stmt_num_rows($result) == 0){
        // Unsuccessful
		$output["success"] = "0";
		$output["message"] = "This group has no posts yet.";
		echo json_encode($output);
        mysqli_close($link);
    }else if(mysqli_stmt_num_rows($result) > 0){

        // find number of likes
        $stmnt_2 = "SELECT COUNT(gpl.POST_LIKE_ID) as NO_OF_LIKES from group_post_like as gpl
        WHERE GROUP_POST_ID = $res_groupPostID
        GROUP BY gpl.POST_LIKE_ID";

        // find number of comments
        $stmnt_3 = "SELECT COUNT(gpc.POST_COMMENT_ID) as NO_OF_COMMENTS from group_post_comment as gpc
        WHERE GROUP_POST_ID = $res_groupPostID
        GROUP BY gpc.POST_COMMENT_ID";

        // LIKES
        $no_likes = 0;
        if( $result_2 = mysqli_query($link,$stmnt_2) ){
            
            echo "In the for loop \n";

            // LIKES
            if( mysqli_num_rows($result_2) > 0 ){
                $no_likes = $result_2->fetch_assoc();
                echo json_encode($no_likes);
            }else{
                $no_likes = 0;
            }
        }

        // COMMENTS
        $no_comments = 0;
        if(($result_3 = mysqli_query($link,$stmnt_2))){

            if( mysqli_num_rows($result_2) > 0 ){
                $no_comments = $result_3->fetch_assoc();
                echo json_encode($no_comments);
            }else{
                $no_comments = 0;
                
            }
        }

        // Display first row item(record)
        $group["USER_FNAME"] = $res_userFname;
        $group["USER_LNAME"] = $res_userLname;
        $group["POST_ID"] = $res_groupPostID;
        $group["POST_TITLE"] = $res_postTitle;
        $group["POST_DATE"] = $res_postDate;
        $group["POST_URL"] = $res_postUrl;
        $group["NO_LIKES"] = $no_likes;
        $group["NO_COMMENTS"] = $no_comments;
        $output[]=$group;

        // Fetch the rest of the row items(records)
        while ($result->fetch()){
            $group["USER_FNAME"] = $res_userFname;
            $group["USER_LNAME"] = $res_userLname;
            $group["POST_ID"] = $res_groupPostID;
            $group["POST_TITLE"] = $res_postTitle;
            $group["POST_DATE"] = $res_postDate;
            $group["POST_URL"] = $res_postUrl;
            $group["NO_LIKES"] = $no_likes;
            $group["NO_COMMENTS"] = $no_comments;
            $output[]=$group;
        }
        
        // Successful
        $display["success"] = "1";
        $display["message"] = $output;
        echo json_encode($display);
        mysqli_close($link);

    }
}

?>