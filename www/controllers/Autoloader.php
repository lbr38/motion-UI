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
        \Controllers\Autoloader::loadConstant();
        \Controllers\Autoloader::register();
        \Controllers\Autoloader::loadSession();
    }

    /**
     *  Chargement du minimum nécessaire pour la page login.php
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
        define('DB', DATA_DIR . '/db/motionui.sqlite');
        define('LOGS_DIR', DATA_DIR . "/logs");
        define('CAMERA_DIR', DATA_DIR . '/configurations');
        define('EVENTS_PICTURES', ROOT . '/public/resources/events-pictures');
        define('DATE_YMD', date('Y-m-d'));
        define('TIME', date('H:i'));
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
        define('UPDATE_SUCCESS_LOG', LOGS_DIR . '/update/update.success');
        define('UPDATE_ERROR_LOG', LOGS_DIR . '/update/update.error');

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
        if (file_exists(DATA_DIR . "/update-running")) {
            define('UPDATE_RUNNING', 'yes');
        } else {
            define('UPDATE_RUNNING', 'no');
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

        if (!is_dir(EVENTS_PICTURES)) {
            mkdir(EVENTS_PICTURES, 0770, true);
        }
    }

    /**
     *  Démarrage et vérification de la session en cours
     */
    private static function loadSession()
    {
        /**
         *  On démarre la session
         */
        if (!isset($_SESSION)) {
            session_start();
        }

        /**
         *  Si les variables de session username ou role sont vides alors on redirige vers la page de login
         */
        if (empty($_SESSION['username']) or empty($_SESSION['role'])) {
            header('Location: login.php');
            exit();
        }

        /**
         *  Si la session a dépassé les 30min alors on redirige vers logout.php qui se chargera de détruire la session
         */
        if (isset($_SESSION['start_time']) && (time() - $_SESSION['start_time'] > 1800)) {
            header('Location: logout.php');
            exit();
        }

        /**
         *  On défini l'heure de création de la session (ou on la renouvelle si la session est toujours en cours)
         */
        $_SESSION['start_time'] = time();
    }
}
