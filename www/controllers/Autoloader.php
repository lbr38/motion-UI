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
        /**
         *  Define a cookie with the actual URI
         *  Useful to redirect to the same page after logout/login
         */
        if (!empty($_SERVER['REQUEST_URI'])) {
            if ($_SERVER["REQUEST_URI"] != '/login' and $_SERVER["REQUEST_URI"] != '/logout') {
                setcookie('origin', parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), array('secure' => true, 'httponly' => true));
            }
        }

        \Controllers\Autoloader::loadConstant();
        \Controllers\Autoloader::register();
        \Controllers\Autoloader::loadSession();
    }

    /**
     *  Chargement du minimum nécessaire pour la page /login
     */
    public static function loadFromLogin()
    {
        \Controllers\Autoloader::loadConstant();
        \Controllers\Autoloader::register();
    }

    /**
     *  Load constants
     */
    private static function loadConstant()
    {
        if (!defined('ROOT')) {
            define('ROOT', dirname(__FILE__, 2));
        }
        if (!defined('DATA_DIR')) {
            define('DATA_DIR', '/var/lib/motionui');
        }
        if (!defined('DB')) {
            define('DB', DATA_DIR . '/db/motionui.sqlite');
        }
        if (!defined('LOGS_DIR')) {
            define('LOGS_DIR', DATA_DIR . "/logs");
        }
        if (!defined('CAMERA_DIR')) {
            define('CAMERA_DIR', DATA_DIR . '/configurations');
        }
        if (!defined('EVENTS_DIR')) {
            define('EVENTS_DIR', DATA_DIR . '/events');
        }
        if (!defined('EVENTS_PICTURES')) {
            define('EVENTS_PICTURES', ROOT . '/public/resources/events-pictures');
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
        if (!defined('LAST_VERSION')) {
            if (file_exists(DATA_DIR . '/version.last')) {
                define('LAST_VERSION', trim(file_get_contents(DATA_DIR . '/version.last')));
            } else {
                define('LAST_VERSION', 'unknown');
            }
        }
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
        if (!defined('UPDATE_SUCCESS_LOG')) {
            define('UPDATE_SUCCESS_LOG', LOGS_DIR . '/update/update.success');
        }
        if (!defined('UPDATE_ERROR_LOG')) {
            define('UPDATE_ERROR_LOG', LOGS_DIR . '/update/update.error');
        }

        /**
         *  Actual URI
         */
        if (!empty($_SERVER['REQUEST_URI'])) {
            if (!defined('__ACTUAL_URI__')) {
                define('__ACTUAL_URI__', parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
            }
        }

        /**
         *  Check if a motion-UI update is running
         */
        if (!defined('UPDATE_RUNNING')) {
            if (file_exists(DATA_DIR . "/update-running")) {
                define('UPDATE_RUNNING', 'yes');
            } else {
                define('UPDATE_RUNNING', 'no');
            }
        }

        /**
         *  Create base directories if not exist
         */
        if (!is_dir(DATA_DIR . '/db')) {
            mkdir(DATA_DIR . '/db', 0770, true);
        }

        if (!is_dir(LOGS_DIR)) {
            mkdir(LOGS_DIR, 0770, true);
        }

        if (!is_dir(LOGS_DIR . '/update')) {
            mkdir(LOGS_DIR . '/update', 0770, true);
        }

        if (!is_dir(CAMERA_DIR)) {
            mkdir(CAMERA_DIR, 0770, true);
        }

        if (!is_dir(EVENTS_DIR)) {
            mkdir(EVENTS_DIR, 0770, true);
        }

        if (!is_dir(EVENTS_PICTURES)) {
            mkdir(EVENTS_PICTURES, 0770, true);
        }
    }

    /**
     *  Start and check actual session
     */
    private static function loadSession()
    {
        /**
         *  Start session
         */
        if (!isset($_SESSION)) {
            session_start();
        }

        /**
         *  If username and role session variables are empty then redirect to login page
         */
        if (empty($_SESSION['username']) or empty($_SESSION['role'])) {
            header('Location: /logout');
            exit();
        }

        /**
         *  If session has reached 60min timeout then redirect to logout page
         */
        if (isset($_SESSION['start_time']) && (time() - $_SESSION['start_time'] > 3600)) {
            header('Location: /logout');
            exit();
        }

        /**
         *  Define the new session start time (or renew the current session)
         */
        $_SESSION['start_time'] = time();
    }
}
