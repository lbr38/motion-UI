<?php

namespace Controllers;

class Autoloader
{
    private static function register()
    {
        /**
         *  Fait appel à la classe Autoloader (cette même classe) et à sa fonction autoload
         */
        spl_autoload_register(function ($className) {

            $className = str_replace('\\', '/', $className);
            $className = str_replace('Models', 'models', $className);
            $className = str_replace('Controllers', 'controllers', $className);

            if (file_exists(ROOT . '/' . $className . '.php')) {
                require_once(ROOT . '/' . $className . '.php');
            }
        });
    }

    public static function load()
    {
        if (!defined('ROOT')) {
            define('ROOT', dirname(__FILE__, 2));
        }
        define('DATA_DIR', '/var/lib/motionui');
        define('DB', DATA_DIR . '/db/motionui.sqlite');
        define('CAMERA_DIR', DATA_DIR . '/configurations');
        define('VERSION', trim(file_get_contents(ROOT . '/version')));

        if (!file_exists(DATA_DIR . '/version.available')) {
            touch(DATA_DIR . '/version.available');
        }

        define('GIT_VERSION', trim(file_get_contents(DATA_DIR . '/version.available')));
        if (defined('VERSION') and defined('GIT_VERSION')) {
            if (VERSION !== GIT_VERSION) {
                if (!defined('UPDATE_AVAILABLE')) {
                    define('UPDATE_AVAILABLE', 'yes');
                }
            } else {
                if (!defined('UPDATE_AVAILABLE')) {
                    define('UPDATE_AVAILABLE', 'no');
                }
            }
        } else {
            define('UPDATE_AVAILABLE', 'no');
        }

        /**
         *  Create base directories if not exist
         */
        if (!is_dir(DATA_DIR . '/db')) {
            mkdir(DATA_DIR . '/db', 0770, true);
        }

        if (!is_dir(CAMERA_DIR)) {
            mkdir(CAMERA_DIR, 0700, true);
        }

        \Controllers\Autoloader::register();
    }
}
