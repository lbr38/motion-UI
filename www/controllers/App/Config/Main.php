<?php

namespace Controllers\App\Config;

use Exception;

class Main
{
    public static function get()
    {
        if (!defined('ROOT')) {
            define('ROOT', dirname(__FILE__, 4));
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
        if (!defined('LOGS_DIR')) {
            define('LOGS_DIR', DATA_DIR . '/logs');
        }
        if (!defined('CAMERAS_DIR')) {
            define('CAMERAS_DIR', DATA_DIR . '/cameras');
        }
        if (!defined('CAPTURES_DIR')) {
            define('CAPTURES_DIR', '/var/lib/motion');
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
            if (defined('VERSION') and defined('GIT_VERSION')) {
                if (preg_match('/^[0-9]+\.[0-9]+\.[0-9]+$/', GIT_VERSION)) {
                    if (VERSION !== GIT_VERSION) {
                        define('UPDATE_AVAILABLE', true);
                    } else {
                        define('UPDATE_AVAILABLE', false);
                    }
                } else {
                    define('UPDATE_AVAILABLE', false);
                }
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
