<?php

namespace Controllers\App\Config;

use Exception;

class Main
{
    public static function get()
    {
        if (!defined('ROOT')) {
            define('ROOT', '/var/www/motionui');
        }
        if (!defined('DATA_DIR')) {
            define('DATA_DIR', '/var/lib/motionui');
        }
        if (!defined('DB_DIR')) {
            define('DB_DIR', DATA_DIR . '/db');
        }
        if (!defined('DB')) {
            define('DB', DB_DIR . '/motionui.sqlite');
        }
        // Websocket server database
        if (!defined('WS_DB')) {
            define('WS_DB', DB_DIR . "/motionui-ws.sqlite");
        }
        if (!defined('LOGS_DIR')) {
            define('LOGS_DIR', DATA_DIR . '/logs');
        }
        // Service logs dir
        if (!defined('SERVICE_LOGS_DIR')) {
            define('SERVICE_LOGS_DIR', LOGS_DIR . '/service');
        }
        // Websocket server logs dir
        if (!defined('WS_LOGS_DIR')) {
            define('WS_LOGS_DIR', LOGS_DIR . '/websocket');
        }
        // Autostart logs dir
        if (!defined('AUTOSTART_LOGS_DIR')) {
            define('AUTOSTART_LOGS_DIR', LOGS_DIR . '/autostart');
        }
        if (!defined('CAMERAS_DIR')) {
            define('CAMERAS_DIR', DATA_DIR . '/cameras');
        }
        if (!defined('CAMERAS_MOTION_CONF_AVAILABLE_DIR')) {
            define('CAMERAS_MOTION_CONF_AVAILABLE_DIR', CAMERAS_DIR . '/motion/conf-available');
        }
        if (!defined('CAMERAS_MOTION_CONF_ENABLED_DIR')) {
            define('CAMERAS_MOTION_CONF_ENABLED_DIR', CAMERAS_DIR . '/motion/conf-enabled');
        }
        if (!defined('CAMERAS_TIMELAPSE_DIR')) {
            define('CAMERAS_TIMELAPSE_DIR', CAMERAS_DIR . '/timelapse');
        }
        if (!defined('CAPTURES_DIR')) {
            define('CAPTURES_DIR', '/var/lib/motion');
        }
        if (!defined('GO2RTC_DIR')) {
            define('GO2RTC_DIR', DATA_DIR . '/go2rtc');
        }
        if (!defined('DB_UPDATE_DONE_DIR')) {
            define('DB_UPDATE_DONE_DIR', DATA_DIR . '/update');
        }
        if (!defined('DATE_YMD')) {
            define('DATE_YMD', date('Y-m-d'));
        }
        if (!defined('TIME')) {
            define('TIME', date('H:i'));
        }
        if (!defined('VERSION')) {
            define('VERSION', trim(file_get_contents(ROOT . '/version')));
        }
        if (!file_exists(DATA_DIR . '/version.available')) {
            touch(DATA_DIR . '/version.available');
            file_put_contents(DATA_DIR . '/version.available', VERSION);
        }
        if (!defined('GIT_VERSION')) {
            define('GIT_VERSION', trim(file_get_contents(DATA_DIR . '/version.available')));
        }
        if (!defined('UPDATE_AVAILABLE')) {
            if (defined('VERSION') and defined('GIT_VERSION') and version_compare(GIT_VERSION, VERSION, '>')) {
                define('UPDATE_AVAILABLE', true);
            } else {
                define('UPDATE_AVAILABLE', false);
            }
        }

        /**
         *  Load system constants
         */
        System::get();
    }
}
