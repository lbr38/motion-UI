<?php

namespace Controllers;

use Exception;

class Autoloader
{
    public function __construct(string $level = 'all')
    {
        $__LOAD_GENERAL_ERROR = 0;
        $__LOAD_ERROR_MESSAGES = array();

        if (!defined('ROOT')) {
            define('ROOT', dirname(__FILE__, 2));
        }

        $this->register();

        /**
         *  Load minimal components
         *  Useful for login/logout
         */
        if ($level == 'minimal') {
            \Controllers\App\Config\Properties::get();
            \Controllers\App\Config\Main::get();
            \Controllers\App\Config\Settings::get();
            \Controllers\App\Structure\Directory::create();
        }

        /**
         *  Load all components
         */
        if ($level == 'all') {
            /**
             *  Define a cookie with the actual URI
             *  Useful to redirect to the same page after logout/login
             */
            if (!empty($_SERVER['REQUEST_URI'])) {
                $origin = $_SERVER['REQUEST_URI'];

                /**
                 *  If the URI is '/login', '/logout' or '/media' then redirect to '/'
                 */
                if (in_array($_SERVER['REQUEST_URI'], array('/login', '/logout', '/media'))) {
                    $origin = '/';
                }

                setcookie('origin', parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), array('secure' => true, 'httponly' => true));
            }

            \Controllers\App\Config\Properties::get();
            \Controllers\App\Config\Main::get();
            \Controllers\App\Config\Settings::get();
            \Controllers\App\Structure\Directory::create();
            \Controllers\App\Session::load();
            \Controllers\App\Config\Log::get();
            \Controllers\App\Config\Notification::get();
        }

        /**
         *  Load components for API
         */
        if ($level == 'api') {
            \Controllers\App\Config\Properties::get();
            \Controllers\App\Config\Main::get();
            \Controllers\App\Config\Settings::get();
            \Controllers\App\Structure\Directory::create();
        }

        if (__LOAD_SETTINGS_ERROR > 0) {
            ++$__LOAD_GENERAL_ERROR;
        }

        if (!defined('__LOAD_GENERAL_ERROR')) {
            define('__LOAD_GENERAL_ERROR', $__LOAD_GENERAL_ERROR);
        }

        unset($__LOAD_GENERAL_ERROR);
    }

    /**
     *  Class autoload
     */
    private function register()
    {
        spl_autoload_register(function ($className) {
            $className = str_replace('\\', '/', $className);
            $className = str_replace('Models', 'models', $className);
            $className = str_replace('Controllers', 'controllers', $className);

            if (file_exists(ROOT . '/' . $className . '.php')) {
                require_once(ROOT . '/' . $className . '.php');
            }
        });
    }
}
