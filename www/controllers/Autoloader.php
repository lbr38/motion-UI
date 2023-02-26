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
        $NOTIFICATION = 0;
        $NOTIFICATION_MESSAGES = array();

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

        /**
         *  Notifications
         */
        if (UPDATE_AVAILABLE == 'true') {
            $message  = '<span class="yellowtext">A new release is available: <b>' . GIT_VERSION . '</b></span>';
            $message .= '<p><br>Update from the terminal on a <b>Debian system</b>:</p>';
            $message .= '<pre>apt update && apt install motionui</pre>';
            $message .= '<p><br>Update from the terminal on a <b>RHEL system</b>:</p>';
            $message .= '<pre>dnf/yum update motionui</pre>';

            $NOTIFICATION++;
            $NOTIFICATION_MESSAGES[] = array('title' => 'Update available', 'message' =>  $message);
        }

        if (!defined('NOTIFICATION')) {
            define('NOTIFICATION', $NOTIFICATION);
        }

        if (!defined('NOTIFICATION_MESSAGES')) {
            define('NOTIFICATION_MESSAGES', $NOTIFICATION_MESSAGES);
        }
    }

    /**
     *  Chargement du minimum nécessaire pour la page /login
     */
    public static function loadMinimal()
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
        if (!defined('CAMERAS_DIR')) {
            define('CAMERAS_DIR', '/etc/motion/cameras');
        }
        if (!defined('EVENTS_DIR')) {
            define('EVENTS_DIR', DATA_DIR . '/events');
        }
        if (!defined('CAPTURES_DIR')) {
            define('CAPTURES_DIR', '/var/lib/motion');
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
                    define('UPDATE_AVAILABLE', 'true');
                }
            } else {
                if (!defined('UPDATE_AVAILABLE')) {
                    define('UPDATE_AVAILABLE', 'false');
                }
            }
        } else {
            define('UPDATE_AVAILABLE', 'false');
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
         *  Create base directories if not exist
         */
        if (!is_dir(DATA_DIR . '/db')) {
            mkdir(DATA_DIR . '/db', 0770, true);
        }

        if (!is_dir(LOGS_DIR)) {
            mkdir(LOGS_DIR, 0770, true);
        }

        if (!is_dir(CAMERAS_DIR)) {
            mkdir(CAMERAS_DIR, 0770, true);

            chgrp(CAMERAS_DIR, 'motion');
            chmod(CAMERAS_DIR, octdec('0770'));
        }

        if (!is_dir(EVENTS_DIR)) {
            mkdir(EVENTS_DIR, 0770, true);

            chgrp(EVENTS_DIR, 'motion');
            chmod(EVENTS_DIR, octdec('0770'));
        }

        if (!is_dir(CAPTURES_DIR)) {
            mkdir(CAPTURES_DIR, 0770, true);

            chmod(CAPTURES_DIR, octdec('0770'));
            chgrp(CAPTURES_DIR, 'motion');
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
