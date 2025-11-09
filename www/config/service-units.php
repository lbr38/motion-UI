<?php
/**
 *  Configure background service units
 *  The unit must have its own Controller class in the \Controllers\Service\Unit namespace
 *  The unit must have a method that hold the logic to execute or call other controllers/services
 */
$units = [
    // This cleans files every hour
    'cleanup-files' => [
        'title' => 'Files cleanup',
        'description' => 'Ensures temporary and old files are cleaned up',
        'controller' => 'Service\Unit\Cleanup\File',
        'method' => 'run',
        'frequency' => 'every-day',
        'time' => '00:00',
        'log-dir' => 'cleanup/files'
    ],
    // This retrieves notifications from github every hour
    'notifications' => [
        'title' => 'Notifications',
        'description' => 'Retrieve new notifications from GitHub',
        'controller' => 'Service\Unit\Notification',
        'method' => 'get',
        'frequency' => 'every-hour'
    ],
    // This monitors CPU, memory and disk usage every minute
    'system-monitoring' => [
        'title' => 'System monitoring',
        'description' => 'Monitors CPU, memory and disk usage every minute',
        'controller' => 'Service\Unit\Monitoring',
        'method' => 'monitor',
        'frequency' => 'every-minute',
        'log-dir' => 'system/monitoring'
    ],
    // This monitors cameras status every minute
    'camera-monitoring' => [
        'title' => 'Camera monitoring',
        'description' => 'Monitors camera status every minute',
        'controller' => 'Service\Unit\Monitoring',
        'method' => 'cameraStatus',
        'frequency' => 'every-minute',
        'log-dir' => 'camera/monitoring'
    ],
    // This monitors motion service status every minute
    'motion-monitoring' => [
        'title' => 'Motion service monitoring',
        'description' => 'Monitors motion service status every minute',
        'controller' => 'Service\Unit\Monitoring',
        'method' => 'motionStatus',
        'frequency' => 'every-hour',
        'log-dir' => 'motion/monitoring'
    ],
    // This checks for new version of the application every hour
    'version-check' => [
        'title' => 'Version check',
        'description' => 'Checks for new version of the application',
        'controller' => 'Service\Unit\Version',
        'method' => 'get',
        'frequency' => 'every-hour',
    ],
    // This runs the autostart function every minute
    'autostart' => [
        'title' => 'Motion service autostart',
        'description' => 'Runs the autostart function to start and stop motion service automatically',
        'controller' => 'Service\Unit\Autostart',
        'method' => 'run',
        'frequency' => 'forever',
        'log-dir' => 'motion/autostart'
    ],
    // This runs the timelapse function every minute
    'timelapse' => [
        'title' => 'Timelapse',
        'description' => 'Runs the timelapse function to capture images at defined intervals',
        'controller' => 'Service\Unit\Timelapse',
        'method' => 'run',
        'frequency' => 'every-minute',
        'log-dir' => 'camera/timelapse'
    ],
    // This runs the websocket server
    'wss' => [
        'title' => 'Websocket server',
        'description' => 'Runs the websocket server to handle real-time communications with browser clients',
        'controller' => 'Service\Unit\WebsocketServer',
        'method' => 'run',
        // Make sure the websocket server is always running
        'frequency' => 'forever'
    ]
];
