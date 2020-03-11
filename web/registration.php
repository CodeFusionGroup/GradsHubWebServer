<?php
/*
$username = "b6febc76a325a3";
$password = "a4831502";
$database = "heroku_6b7ffb41be0156e";
$host = "us-cdbr-iron-east-04.cleardb.net";
$link = mysqli_connect($host, $username, $password, $database);
*/

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$link  = new mysqli($server, $username, $password, $db);



$user_fname = $_REQUEST["USER_FNAME"];
$user_lname = $_REQUEST["USER_LNAME"];
$user_password = $_REQUEST["USER_PASSWORD"];
$user_email = $_REQUEST["USER_EMAIL"];
$user_phone_no = $_REQUEST["USER_PHONE_NO"];
$user_acad_status = $_REQUEST["USER_ACAD_STATUS"];
//Hash the password
$hashed_password = password_hash($user_password,PASSWORD_DEFAULT);

$query = "INSERT INTO USER (USER_FNAME,USER_LNAME,USER_PASSWORD,USER_EMAIL,USER_PHONE_NO,USER_ACAD_STATUS) VALUES($user_fname,$user_lname,$hashed_password,$user_email,$user_phone_no,$user_acad_status)";

echo "Before query";

if($result = mysqli_query($link,$query)){
    $output["success"]="1";
    $output["message"]="Registration successful!";
    echo json_encode($output);
    mysqli_close($link);

}



?>