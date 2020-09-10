<?php
    class Push{

        //Notification title
        private $title;

        //Notification message
        private $message;

        // Initialise values in the constructor
        function __construct($title, $message){
            $this->title = $title;
            $this->message = $message;
        }

        // TODO: Change to specific message keys for android
        // Get Push notification
        public function getMessage(){
            $result = array();
            // The message data payload
            $result['title'] = $this->title;
            $result['message'] = $this->message;
            return $result;
        }

        //Test Notification
        public function getNotification(){
            $result = array();
            // The notification data payload
            $result['title'] = $this->title;
            $result['body'] = $this->message;
            $result['sound'] = "default";
            $result['type'] = 1;
            return $result;
        }

    }
?>