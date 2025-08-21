<?php
cli_set_process_title('motionui.service');

define('ROOT', '/var/www/motionui');
require_once(ROOT . '/controllers/Autoloader.php');
new \Controllers\Autoloader('minimal');

use Controllers\Log\Cli as CliLog;

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
     *  Run autostart task
     */
    if (!empty($argv[1]) && $argv[1] == 'autostart') {
        cli_set_process_title('motionui.autostart');
        $mymotionAutostart->autostart();
        exit;
    }

    /**
     *  Run monitoring service
     */
    if (!empty($argv[1]) && $argv[1] == 'system-monitoring') {
        cli_set_process_title('motionui.system-monitoring');
        $monitoringService = new \Controllers\Service\Monitoring();
        $monitoringService->execute();
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
     *  Run main service
     */
    $myservice->run();
} catch (Exception | Error $e) {
    CliLog::error('Service general error', $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL);
    $myLogController->log('error', 'Service', 'General error: ' . $e->getMessage(), $e->getTraceAsString());
    exit(1);
}

exit;
