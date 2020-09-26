<?php
    
    // Configuration for Global variables
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';
    // Get the User class
    include_once $_SERVER['DOCUMENT_ROOT'] . '/class/user.php';

    // Create User object
    $user_obj = new User();

    // Check if data is sent
    if( isset($_GET["key"],$_GET["email"],$_GET["action"]) && ($_GET["action"]=="reset") && !isset($_POST["action"]) ){
        
        //Form data
        $key = $_GET["key"];
        $email = $_GET["email"];
        $cur_date = date("Y-m-d H:i:s");

        //Check if password recovery exists
        $stmnt = $user_obj->recoveryExist($email, $key);
        $stmnt_count = $stmnt->rowCount();

        if( $stmnt_count > 0){

            // Check the expiry
            $data_row = $stmnt->fetch(PDO::FETCH_ASSOC);
            $exp_date = $data_row['RECOVERY_EXP_DATE'];

            if($exp_date >= $cur_date){
                ?>


                <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <meta name="description" content="Gradshub Password Recovery">
                        <title>Password Recovery</title>

                        <!-- Bootstrap CSS -->
                        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

                        <!-- Custom Sytling -->
                        <style>
                            .bd-placeholder-img {
                            font-size: 1.125rem;
                            text-anchor: middle;
                            -webkit-user-select: none;
                            -moz-user-select: none;
                            -ms-user-select: none;
                            user-select: none;
                            }
                    
                            @media (min-width: 768px) {
                            .bd-placeholder-img-lg {
                                font-size: 3.5rem;
                            }
                            }
                        </style>

                        <!-- Custom styles for this template -->
                        <link href="../../assets/passwordForm.css" rel="stylesheet">

                    </head>
                    <body class="text-center">

                        <form class="form-signin" method="post" action="" name="update">
                            <img class="mb-4" src="../../assets/applogo.png" alt="Gradshub Logo" width="72" height="72">
                            <h1 class="h3 mb-4 font-weight-normal">Password Recovery</h1>
                            <input type="hidden" name="action" value="update" />
                            
                            <label for="inputPassword1" class ="sr-only">Enter New Password:</label>
                            <input id="inputPassword1" class="form-control" placeholder="New Password" type="password" name="pass1" maxlength="15" required autofocus />
                            
                            <label for="inputPassword2" class ="sr-only">Re-Enter New Password:</label>
                            <input id="inputPassword2" class="form-control" placeholder="Re-Enter New Password" type="password" name="pass2" maxlength="15" required/>
                            
                            <input type="hidden" name="email" value="<?php echo $email;?>"/>
                            
                            <button class="btn btn-lg btn-primary btn-block" type="submit" value="Reset Password">Submit</button>
                            <p class="mt-5 mb-3 text-muted">&copy; Gradshub-2020</p>
                        </form>

                    </body>
                </html>


                <?php
            }else{
                readfile("../../templates/linkExpired.html");
            }
            
        }else{
            readfile("../../templates/linkInvalid.html");
        }

    }
    // else{
    //     $error = '<h2>Error</h2>
    //         <p>Invalid Link</p>';
    //         echo $error;
    // }

    // Update/change password
    if( isset($_POST["email"],$_POST["action"])  && ($_POST["action"]=="update") ) {

        $email = $_POST["email"];
        $pass1 = $_POST["pass1"];
        $pass2 = $_POST["pass2"];

        if($pass1 == $pass2){

            // Hash the password
            $hashed_password = password_hash($pass1,PASSWORD_DEFAULT);

            //Get user using email
            $stmnt_user = $user_obj->getUserByEmail($email);
            $data_row_user = $stmnt_user->fetch(PDO::FETCH_ASSOC);
            $user_id = $data_row_user['USER_ID'];

            //Update password
            if($user_obj->updatePassword($user_id,$hashed_password)){

                // Delete recovery record 
                //TODO: Change to an update of the record
                if($user_obj->deleteRecovery($email)){
                    readfile("../../templates/passwordSuccess.html");
                }else{
                    echo 'Could not recover password';
                }
                
            }else{
                echo 'Could not update password';
            }

        }else{
            $error = '<p>Passwords do not match, both passwords should be same.<br /><br /></p>';
            echo $error;
        }

    }else{

    }
?>