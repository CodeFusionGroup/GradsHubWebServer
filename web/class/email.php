<?php

    //importing the variables files
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';

    // Include the composer generated autoload.php file
    require(__DIR__ . '../../../vendor/autoload.php');

    //Import PHPMailer classes into the global namespace
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    class Email{

        // Properties
        private $mailer;
        
        public function __construct(){

            //create the PHPMailer class
            $this->mailer = new PHPMailer();

            //SMTP configuration and Server settings
            $this->mailer->isSMTP();
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $this->mailer->Host = 'smtp.gmail.com';
            $this->mailer->Port = 587;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = EMAIL_ADDRESS;
            $this->mailer->Password = EMAIL_PASSWORD;
        }

        // #################### SEND EMAIL ####################

        //Send an email to recover password
        public function passwordRecovery($query_email,$key,$query_name){

            //Recipients
            $this->mailer->setFrom('no-reply@gradshub.com', 'Gradshub Support');
            $this->mailer->addAddress($query_email, $query_name);
            $this->mailer->Subject = 'GradsHub: Password Recovery';

            // https://gradshub.herokuapp.com
            // http://localhost:8080
            // Content
            $this->mailer->isHTML(true);
            $content = '
            <html>
            <head>
            <title>GradsHub: Password Recovery</title>
            </head>
            <body>
            <p>Dear ' .$query_name. ',</p>
            <p>To change your password please click the link below:</p>
            <p>-------------------------------------------------------------</p>
            <p> 
                <a href = "https://gradshub.herokuapp.com/api/User/password-recovery.php?key='. $key . '&email='.$query_email.'&action=reset" target="_blank"> 
                https://gradshub.herokuapp.com/api/User/password-recovery.php?key='. $key . '&email='.$query_email.'&action=reset
                </a> 
            </p>
            <p>-------------------------------------------------------------</p>
            <p>Please note that for security reasons the link will expire in one day(24 hours).</p>
            <p>If you did not request this password recovery link, no action 
            is needed, your password will not be reset. However, you may want to log into 
            your account and change your password as a precaution.</p>
            <p>Kind regards</p>
            <p>Gradshub Team</p>
            </body>
            </html>';
            $this->mailer->Body = $content;

            if ($this->mailer->send()) {
                return true;
                // Save message to 'Password Recovery' folder
                save_mail($this->mailer,'Password');
            }
            // echo 'Mailer Error: '. $mail->ErrorInfo;
            return false;

        }

        public function userVerification($query_email,$code,$query_name){

            //Recipients
            $this->mailer->setFrom('no-reply@gradshub.com', 'Gradshub Support');
            $this->mailer->addAddress($query_email, $query_name);
            $this->mailer->Subject = 'GradsHub: Account Verification';

            // https://gradshub.herokuapp.com
            // http://localhost:8080
            // https://gradshub.herokuapp.com/api/User/password-recovery.php?key='. $key . '&email='.$query_email.'&action=reset
            // Content
            $this->mailer->isHTML(true);
            $content = '
            <html>
            <head>
            <title>GradsHub: Account Verification</title>
            </head>
            <body>
            <p>Dear ' .$query_name. ',</p>
            <p>Please click The following link to verify your email and activate your account</p>
            <p>-------------------------------------------------------------</p>
            <p> 
                <a href = "https://gradshub.herokuapp.com/api/User/email-verification.php?code='. $code . '&email='.$query_email.'&action=verify" target="_blank"> 
                Click Here!
                </a> 
            </p>
            <p>-------------------------------------------------------------</p>
            <p>Please note that for security reasons the link will expire in one day(24 hours).</p>
            <p>If you did not request to create an account with us, no action 
            is needed, this link will expire and you will recieve no further communication from us.</p>
            <p>Should this persist please contact us. <a href = "mailto:gradshub.team@gmail.com?subject=Persistent Account Verification Emails">Send Email</a></p>
            <p>Kind regards</p>
            <p>Gradshub Team</p>
            </body>
            </html>';
            $this->mailer->Body = $content;

            if ($this->mailer->send()) {
                return true;
                // Save message to 'Password Recovery' folder
                save_mail($this->mailer,'Verification');
            }
            // echo 'Mailer Error: '. $mail->ErrorInfo;
            return false;

        }

        // #################### USEFUL FUNCTIONS ####################

        // Saves an email to sepcific folder in gmail
        function save_mail($mail,$folder){

            // Path to save email to a specific tag/folder
            $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/'. $folder;

            // )pen an IMAP connection using the same username and password used for SMTP
            $imapStream = imap_open($path, $mail->Username, $mail->Password);

            $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
            imap_close($imapStream);

            return $result;
        }

    }






?>