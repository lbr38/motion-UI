<?php
/**
 *  Manually check and initialize main database
 */

define('ROOT', '/var/www/motionui');

require_once(ROOT . '/controllers/Autoloader.php');
new \Controllers\Autoloader('minimal');

try {
    $databases = array('main', 'ws');

    /**
     *  Open a connection to each database and create tables if they do not exist
     */
    foreach ($databases as $database) {
        $myconn = new \Models\Connection($database);
    }
} catch (\Exception $e) {
    echo 'There was an error while initializing ' . $database . ' database: ' . $e->getMessage() .  PHP_EOL;
    exit(1);
}

echo PHP_EOL . 'Databases check and initialization successful' . PHP_EOL;
exit(0);
