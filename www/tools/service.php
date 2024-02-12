<?php
cli_set_process_title('motionui.service');

define('ROOT', dirname(__FILE__, 2));
require_once(ROOT . '/controllers/Autoloader.php');
new \Controllers\Autoloader('minimal');

$mysignalhandler = new \Controllers\SignalHandler();
$myservice = new \Controllers\Service\Service();
$mymotionAutostart = new \Controllers\Motion\Autostart();

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
 *  Run main service
 */
$myservice->run();

exit;
