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

        // Get Push notification
        public function getPush(){
            $result = array();
            $result['data']['title'] = $this->title;
            $result['data']['message'] = $this->message;
            return $result;
        }

        //Test Notification
        public function testNot(){
            $result = array();
            $result['notification']['title'] = $this->title;
            $result['notification']['body'] = $this->message;
            return $result;
        }

    }
?>