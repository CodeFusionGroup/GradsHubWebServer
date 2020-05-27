<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);


$stmnt = " SELECT u.USER_FNAME, u.USER_LNAME, gp.GROUP_POST_ID,gp.POST_TITLE, gp.POST_DATE,gp.POST_ATTACHMENT_URL,
COUNT(gpc.POST_COMMENT_ID) as NO_OF_COMMENTS,COUNT(gpl.POST_LIKE_ID) as NO_OF_LIKES 
FROM group_post as gp 
INNER JOIN group_user as gu ON gp.GROUP_USER_ID = gu.GROUP_USER_ID
INNER JOIN user as u ON gu.USER_ID = u.USER_ID
LEFT JOIN group_post_comment as gpc ON gp.GROUP_POST_ID = gpc.GROUP_POST_ID
LEFT OUTER JOIN group_post_like as gpl ON gp.GROUP_POST_ID = gpl.GROUP_POST_ID
where gp.GROUP_ID = ?
GROUP BY gp.GROUP_POST_ID ";



if ( $result = mysqli_prepare($link,$stmnt)){

    mysqli_stmt_bind_param($result,"i",$group_id);
    $group_id = $_REQUEST["GROUP_ID"];

    mysqli_stmt_execute($result);
    mysqli_stmt_store_result($result);

    mysqli_stmt_bind_result($result,$res_userFname,$res_userLname,$res_groupPostID,$res_postTitle,$res_postDate
    ,$res_postUrl,$res_noComments,$res_noLikes);
    mysqli_stmt_fetch($result);


    if(mysqli_stmt_num_rows($result) == 0){
        // Unsuccessful
		$output["success"] = "0";
		$output["message"] = "This group has no posts yet.";
		echo json_encode($output);
        mysqli_close($link);
    }else if(mysqli_stmt_num_rows($result) > 0){

        // Display first row item(record)
        $group["USER_FNAME"] = $res_userFname;
        $group["USER_LNAME"] = $res_userLname;
        $group["POST_ID"] = $res_groupPostID;
        $group["POST_TITLE"] = $res_postTitle;
        $group["POST_DATE"] = $res_postDate;
        $group["POST_URL"] = $res_postUrl;
        $group["NO_COMMENTS"] = $res_noComments;
        $group["NO_LIKES"] = $res_noLikes;
        $output[]=$group;

        // Fetch the rest of the row items(records)
        while ($result->fetch()){
            $group["USER_FNAME"] = $res_userFname;
            $group["USER_LNAME"] = $res_userLname;
            $group["POST_ID"] = $res_groupPostID;
            $group["POST_TITLE"] = $res_postTitle;
            $group["POST_DATE"] = $res_postDate;
            $group["POST_URL"] = $res_postUrl;
            $group["NO_COMMENTS"] = $res_noComments;
            $group["NO_LIKES"] = $res_noLikes;;
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