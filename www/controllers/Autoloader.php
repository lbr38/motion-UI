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
        if (VERSION == "2.3.7") {
            $NOTIFICATION++;
            $message  = '<p>Starting from release version <b>2.3.7</b> you will have to install/update Motion-UI using the provided deb/rpm package from my repository. <br><br></p>';
            $message .= '<p>Further updates will still be notified here but you will have to update using the deb/rpm package provided.<br><br></p>';
            $message .= '<p>Check how to install or update Motion-UI using my repository: <b><a href="https://github.com/lbr38/motion-UI/wiki/Documentation">documentation<img src="resources/icons/external-link.svg" class="icon"></a></b></p>';
            $NOTIFICATION_MESSAGES[] = array('title' => 'Important', 'message' => $message);
        }
        if (GIT_VERSION == "3.0.0") {
            $NOTIFICATION++;
            $message  = '<p class="yellowtext">Breaking changes release:<br><br></p>';
            $message .= '<p><b>3.0.0</b> is a major release version. <b>It is not compatible with any previous motion-UI version</b> and brings no migration tool.<br> Installation will <b>backup your actual configuration</b> then make a <b>fresh new install of motion and motion-UI.</b><br><br></p>';
            $message .= '<p class="yellowtext">All your configuration and events will be lost and you will have to setup all of your cameras again.<br><br></p>';
            $message .= '<p>You can read release changelog here: <b><a href="https://github.com/lbr38/motion-UI/releases/tag/3.0.0">changelog<b><img src="resources/icons/external-link.svg" class="icon"></p>';
            $NOTIFICATION_MESSAGES[] = array('title' => 'Important', 'message' => $message);
        }
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
