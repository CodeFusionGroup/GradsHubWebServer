<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

// init some vars
$output = array();

// Find groups that user doesnt belong to
$stmnt = "SELECT rg.RESEARCH_GROUP_ID,rg.GROUP_NAME, rg.GROUP_VISIBILITY FROM research_group rg
WHERE rg.RESEARCH_GROUP_ID NOT IN(
SELECT gu.RESEARCH_GROUP_ID FROM group_user gu 
INNER JOIN user u ON gu.USER_ID = u.USER_ID
WHERE gu.USER_ID = ? )";
if($result = mysqli_prepare($link,$stmnt)){

    mysqli_stmt_bind_param($result,"i",$user_id);
    $user_id = $_REQUEST["USER_ID"];

    mysqli_stmt_execute($result);
    mysqli_stmt_store_result($result);

    mysqli_stmt_bind_result($result,$res_groupID,$res_groupName,$res_groupVis);
    mysqli_stmt_fetch($result);

    // If there are any available groups
    if(mysqli_stmt_num_rows($result) > 0){

        // Display first row item(record)
        $group["GROUP_ID"] = $res_groupID;
        $group["GROUP_NAME"] = $res_groupName;
        $group["GROUP_VISIBILITY"] = $res_groupVis;
        $output[]=$group;

        // Fetch the rest of the row items(records)
        while ($result->fetch()){
            $group["GROUP_ID"] = $res_groupID;
            $group["GROUP_NAME"] = $res_groupName;
            $group["GROUP_VISIBILITY"] = $res_groupVis;
            $output[]=$group;
        }
        
        // Successful
        $display["success"] = "1";
        $display["message"] = $output;
        echo json_encode($display);
        mysqli_close($link);

    }else{

        // Unsuccessful
        $display["success"] = "0";
        $display["message"] = "No available groups.";
        echo json_encode($display);
        mysqli_close($link);

    }
}
?>