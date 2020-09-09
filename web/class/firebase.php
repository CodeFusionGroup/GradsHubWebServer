<?php
    class Firebase{

        public function send($reg_tokens,$message){
            $fields = array(
                'registration_ids' => $reg_tokens,
                'data' => $message,
                'direct_book_ok' => true
            );
            return $this->sendPushNotification($fields);
        }

        public function notification($reg_tokens,$notification){
            $fields = array(
                'message' => $notification,
                'token' => $reg_tokens[0]
            );
            return $this->testNotification($fields);
        }

        /*
        * This function will make the actuall curl request to firebase server
        * and then the message is sent 
        */
        private function sendPushNotification($fields) {
            
            //importing the variables files
            require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';

            //firebase server url to send the curl request
            $url = 'https://fcm.googleapis.com/fcm/send';
    
            //building headers for the request
            $headers = array(
                'Authorization: key=' . FIREBASE_API_KEY,
                'Content-Type: application/json'
            );

            //Initializing curl to open a connection
            $ch = curl_init();
    
            //Setting the curl url
            curl_setopt($ch, CURLOPT_URL, $url);
            
            //setting the method as post
            curl_setopt($ch, CURLOPT_POST, true);

            //adding headers 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            //disabling ssl support
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            //adding the fields in json format 
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    
            //finally executing the curl request 
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }
    
            //Now close the connection
            curl_close($ch);
    
            //and return the result 
            return $result;
        }

        private function testNotification($fields){
            //importing the variables files
            require_once $_SERVER['DOCUMENT_ROOT'] . '/config/vars.php';

            //firebase server url to send the curl request
            $url = 'https://fcm.googleapis.com/v1/projects/gradshub-98c26/messages:send';

            //building headers for the request
            $headers = array(
                'Authorization: Bearer ' . FIREBASE_API_KEY,
                'Content-Type: application/json'
            );
            
            //Initializing curl to open a connection
            $ch = curl_init();
    
            //Setting the curl url
            curl_setopt($ch, CURLOPT_URL, $url);
            
            //setting the method as post
            curl_setopt($ch, CURLOPT_POST, true);

            //adding headers 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            //disabling ssl support
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            //adding the fields in json format 
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    
            //finally executing the curl request 
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }
    
            //Now close the connection
            curl_close($ch);
    
            //and return the result 
            return $result;
        }

    }
    
?>