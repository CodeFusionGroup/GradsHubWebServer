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
                <br />
                <form method="post" action="" name="update">
                <input type="hidden" name="action" value="update" />
                <br /><br />
                <label><strong>Enter New Password:</strong></label><br />
                <input type="password" name="pass1" maxlength="15" required />
                <br /><br />
                <label><strong>Re-Enter New Password:</strong></label><br />
                <input type="password" name="pass2" maxlength="15" required/>
                <br /><br />
                <input type="hidden" name="email" value="<?php echo $email;?>"/>
                <input type="submit" value="Reset Password" />
                </form>
                <?php
            }else{
                $error = "<h2>Link Expired</h2>
                <p>The link is expired. You are trying to use an expired link which 
                is valid for 24 hours only(1 day after request).<br /><br /></p>";
                echo $error;
            }
            
        }else{
            $error = '<h2>Invalid Link</h2>
            <p>The link is invalid/expired. Either you did not copy the correct link
            from the email, or you have already used the key in which case it is 
            deactivated.</p>';
            echo $error;
        }

    }else{
        $error = '<h2>Error</h2>
            <p>Invalid Link</p>';
            echo $error;
    }

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
                echo '<p>Congratulations! Your password has been updated successfully.</p>';
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