<?php
cli_set_process_title('motionui.service');

define('ROOT', '/var/www/motionui');
require_once(ROOT . '/controllers/Autoloader.php');
new \Controllers\Autoloader('minimal');

$mysignalhandler = new \Controllers\SignalHandler();
$myservice = new \Controllers\Service\Service();
$mymotionAutostart = new \Controllers\Motion\Autostart();
$mycameraTimelapse = new \Controllers\Camera\Timelapse();
$myLogController = new \Controllers\Log\Log();

try {
    /**
     *  Define a file to create on interrupt
     *  This file is used to stop stats parsing
     */
    $mysignalhandler->touchFileOnInterrupt(DATA_DIR . '/.service-autostart-stop');

    /**
     *  Run websocket server
     */
    if (!empty($argv[1]) && $argv[1] == 'wss') {
        cli_set_process_title('motionui.wss');

        /**
         *  Start websocket server on port 8085 (8081 is already used by motion)
         */
        $websockerServer = new \Controllers\Websocket\WebsocketServer();
        $websockerServer->run(8085);
        exit;
    }

    /**
     *  Run autostart task
     */
    if (!empty($argv[1]) && $argv[1] == 'autostart') {
        cli_set_process_title('motionui.autostart');
        $mymotionAutostart->autostart();
        exit;
    }

    /**
     *  Run timelapse task
     */
    if (!empty($argv[1]) && $argv[1] == 'timelapse') {
        cli_set_process_title('motionui.timelapse');
        $mycameraTimelapse->timelapse();
        exit;
    }

    /**
     *  Run main service
     */
    $myservice->run();
} catch (Exception $e) {
    $myLogController->log('error', 'Service', "General exception: " . $e->getMessage());
    exit(1);
} catch (Error $e) {
    $myLogController->log('error', 'Service', "General error: " . $e->getMessage());
    exit(1);
}

exit;
