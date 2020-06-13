<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// Variable(s)
$post_counts_arr = array();
$full_post_arr = array();

// Reuest the group id
$group_id = $_REQUEST["GROUP_ID"];

// Select all posts in a group
$stmnt_post = " SELECT u.USER_FNAME, u.USER_LNAME, gp.GROUP_POST_ID,gp.POST_TITLE, gp.POST_DATE,gp.POST_ATTACHMENT_URL
FROM group_post as gp 
INNER JOIN group_user as gu ON gp.GROUP_USER_ID = gu.GROUP_USER_ID
INNER JOIN user as u ON gu.USER_ID = u.USER_ID
where gp.GROUP_ID = $group_id
ORDER BY gp.POST_DATE DESC ";

// Find no. comments and likes in a group post
$stmnt_count = " SELECT COALESCE( NO_OF_COMMENTS,0) AS NO_OF_COMMENTS,COALESCE( NO_OF_LIKES,0) AS NO_OF_LIKES
FROM group_post AS gp
LEFT JOIN (
SELECT GROUP_POST_ID,COUNT(*) AS NO_OF_COMMENTS
FROM group_post_comment
GROUP BY GROUP_POST_ID 
) NO_OF_COMMENTS ON NO_OF_COMMENTS.GROUP_POST_ID = gp.GROUP_POST_ID
LEFT JOIN (
SELECT GROUP_POST_ID,COUNT(*) AS NO_OF_LIKES
FROM group_post_like
GROUP BY GROUP_POST_ID 
) NO_OF_LIKES ON NO_OF_LIKES.GROUP_POST_ID = gp.GROUP_POST_ID
WHERE gp.GROUP_ID = $group_id
ORDER BY gp.POST_DATE DESC ";

// Make the queries
$query_post = mysqli_query($link,$stmnt_post );
$query_count = mysqli_query($link,$stmnt_count );

if($query_post && $query_count){

    // No posts in this group
    if(mysqli_num_rows($query_post) == 0){
        // Unsuccessful
        $output["success"] = "0";
        $output["message"] = "This group has no posts yet.";
        echo json_encode($output);
        mysqli_close($link);

    }else if(mysqli_num_rows($query_post) > 0){

        // Fetch the post info
        while ($row=$query_post->fetch_assoc()){
            $post_info = $row;
            // Push info into an array
            array_push($full_post_arr,$post_info);
        }

        // Fetch the no of likes and comments
        while ($row=$query_count->fetch_assoc()){
            $post_counts = $row;
            // Push counts into an array
            array_push($post_counts_arr,$post_counts);
        }

        // Combine everything into one array
        for($i = 0 ;$i < count($full_post_arr); $i++ ){

            $full_post_arr[$i]["NO_OF_COMMENTS"] = $post_counts_arr[$i]["NO_OF_COMMENTS"];
            $full_post_arr[$i]["NO_OF_LIKES"] = $post_counts_arr[$i]["NO_OF_LIKES"];

        }

        // Successful
        $display["success"] = "1";
        $display["message"] = $full_post_arr;
        echo json_encode($display);
        mysqli_close($link);

    }

}else{
    echo "Error with the queries.";
}

?>