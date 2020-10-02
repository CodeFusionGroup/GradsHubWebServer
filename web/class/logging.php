<?php
    
    // Include the composer generated autoload.php file
    require('../../../vendor/autoload.php');
            
    //Import Monolog classes into the global namespace
    use Monolog\Logger;
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\StreamHandler;

    class Logging{

        // Properties
        private $formatter;
        private $logger;
        // private $syslogHandler;

        // Defalt contructor
        public function __construct(){

            // Set the format
            $output = "%message%";
            $this->formatter = new LineFormatter($output);

            // create a log channel to STDOUT
            $this->logger = new Logger('Gradshub_logs');
            $streamHandler = new StreamHandler('php://stdout', Logger::DEBUG);
            $streamHandler->setFormatter($formatter);
            $this->logger->pushHandler($streamHandler);
        }

        // Add a log
        public function infoLog($message){
            $this->logger->info($message);
        }

        public function warningLog($message){
            $this->logger->warning($message);
        }

        public function errorLog($message){
            $this->logger->error($message);
        }


    }




?>