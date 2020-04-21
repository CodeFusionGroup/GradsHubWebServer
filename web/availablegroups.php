<?php
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);

$output = array();

if($result = mysqli_prepare($link,"SELECT rg.GROUP_NAME FROM research_group rg
INNER JOIN group_user gu ON rg.RESEARCH_GROUP_ID = gu.RESEARCH_GROUP_ID
WHERE gu.USER_ID != ? ")){

    mysqli_stmt_bind_param($result,"i",$user_id);
    $user_id = $_REQUEST["USER_ID"];

    mysqli_stmt_execute($result);
    mysqli_stmt_store_result($result);

    mysqli_stmt_bind_result($result,$res_groupName);
    mysqli_stmt_fetch($result);

    // Checks if there are any available groups
    if(mysqli_stmt_num_rows($result) > 0){
        echo "Groups are available";

        // $row=$result->fetch_assoc()
        while ($row=$result->fetch_assoc()){
            $output[]=$row;
        }
        // $display["success"] = "1";
        // $display["message"] = $output;
        // echo json_encode($display);
        echo json_encode($output);
        mysqli_close($link);

    }else{

        $display["success"] = "0";
        $display["message"] = "No available groups.";
        echo json_encode($display);
        mysqli_close($link);

    }
}
?>