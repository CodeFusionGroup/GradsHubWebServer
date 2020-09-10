<?php 

    // Root of the server
    define('SERVER_ROOT',__DIR__);
    // Root of the public api
    define('WEB_ROOT', SERVER_ROOT.'/web');

    //Database URL
    $url = parse_url(getenv("CLEARDB_DATABASE_URL"));

    if(sizeof($url) == 6){

        // Get database login information
        define('DB_HOST',$url["host"]);
        define('DB_NAME',substr($url["path"], 1));
        define('DB_USERNAME',$url["user"]);
        define('DB_PASSWORD',$url["pass"]);
        // Firebase api key
        define('FIREBASE_API_KEY',getenv("FIREBASE_SERVER_KEY"));

    }else{

        // TODO: Use local DB
        require_once dirname(__FILE__) . "/localvars.php";
        define('DB_HOST',TMP_HOST);
        define('DB_NAME',TMP_NAME);
        define('DB_USERNAME',TMP_USERNAME);
        define('DB_PASSWORD',TMP_PASSWORD);
        // Firebase api key
        define('FIREBASE_API_KEY',FIREBASE_KEY);

    }
    
?>