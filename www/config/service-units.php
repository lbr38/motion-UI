<?php
/**
 *  Configure background service units
 *  The unit must have its own Controller class in the \Controllers\Service\Unit namespace
 *  The unit must have a method that hold the logic to execute or call other controllers/services
 */
$units = [
    // This cleans temporary files every hour
    'cleanup-temp-files' => [
        'title' => 'Temporary files cleanup',
        'description' => 'Ensures temporary files under Motion-UI data directory are cleaned',
        'controller' => 'Service\Unit\Cleanup\File',
        'method' => 'run',
        'interval' => 'every-day',
        'time' => '00:00',
        'log-dir' => 'cleanup/temporary-files'
    ],
    // This retrieves notifications from github every hour
    'notifications' => [
        'title' => 'Notifications',
        'description' => 'Retrieve new notifications from GitHub',
        'controller' => 'Service\Unit\Notification',
        'method' => 'get',
        'interval' => 'every-hour'
    ],
    // This monitors CPU, memory and disk usage every minute
    'system-monitoring' => [
        'title' => 'System monitoring',
        'description' => 'Monitors CPU, memory and disk usage every minute',
        'controller' => 'Service\Unit\Monitoring',
        'method' => 'monitor',
        'interval' => 'every-minute',
        'log-dir' => 'system/monitoring'
    ],
    // This monitors motion service status every minute
    'motion-monitoring' => [
        'title' => 'Motion service monitoring',
        'description' => 'Monitors motion service status every minute',
        'controller' => 'Service\Unit\Monitoring',
        'method' => 'motionStatus',
        'interval' => 'every-hour',
        'log-dir' => 'motion/monitoring'
    ],
    // This checks for new version of the application every hour
    'version-check' => [
        'title' => 'Version check',
        'description' => 'Checks for new version of the application',
        'controller' => 'Service\Unit\Version',
        'method' => 'get',
        'interval' => 'every-hour',
    ],
    // This runs the autostart function every minute
    'autostart' => [
        'title' => 'Motion service autostart',
        'description' => 'Runs the autostart function to start and stop motion service automatically',
        'controller' => 'Service\Unit\Autostart',
        'method' => 'run',
        'interval' => 'every-minute',
        'log-dir' => 'motion/autostart'
    ],
    // This runs the timelapse function every minute
    'timelapse' => [
        'title' => 'Timelapse',
        'description' => 'Runs the timelapse function to capture images at defined intervals',
        'controller' => 'Service\Unit\Timelapse',
        'method' => 'run',
        'interval' => 'every-minute',
        'log-dir' => 'camera/timelapse'
    ],
    // This runs the websocket server
    'wss' => [
        'title' => 'Websocket server',
        'description' => 'Runs the websocket server to handle real-time communications with browser clients',
        'controller' => 'Service\Unit\WebsocketServer',
        'method' => 'run',
        // Make sure the websocket server is always running
        'interval' => 'every-minute'
    ]
];
