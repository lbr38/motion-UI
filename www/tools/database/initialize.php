<?php
/**
 *  Manually check and initialize main database
 */

define('ROOT', '/var/www/motionui');

require_once(ROOT . '/controllers/Autoloader.php');
new \Controllers\Autoloader('minimal');
use \Controllers\Log\Cli as CliLog;

try {
    $databases = array('main', 'ws');

    /**
     *  Open a connection to each database and create tables if they do not exist
     */
    foreach ($databases as $database) {
        $myconn = new \Models\Connection($database);
    }
} catch (Exception $e) {
    CliLog::error('There was an error while initializing ' . $database . ' database', $e->getMessage());
    exit(1);
}

CliLog::log('Databases check and initialization successful');

exit(0);
