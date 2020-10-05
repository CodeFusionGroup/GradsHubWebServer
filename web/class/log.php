<?php
    
    // Include the composer generated autoload.php file
    require(__DIR__ . '../../../vendor/autoload.php');
            
    //Import Monolog classes into the global namespace
    use Monolog\Logger;
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\StreamHandler;
    // use Monolog\Handler\FirePHPHandler;

    class Log{

        // Properties
        private $logger;

        // Defalt contructor
        public function __construct(){

            // Set the format
            $output = "[%channel%]:%level_name% > %message% \n";
            $formatter = new LineFormatter($output);

            // create the main log channel to STDOUT
            $this->logger = new Logger('GradshubServer_logs');
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