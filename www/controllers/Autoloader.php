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
            // $className = str_replace('Models', 'models', $className);
            $className = str_replace('Controllers', 'controllers', $className);

            if (file_exists(ROOT . '/' . $className . '.php')) {
                require_once(ROOT . '/' . $className . '.php');
            }
        });
    }

    public static function load()
    {
        define('ALERT_INI', '/etc/motion/alert.ini');
        define('CAMERA_DIR', ROOT . '/configurations');

        /**
         *  Création des répertoires de base si n'existent pas
         */
        if (!is_dir(CAMERA_DIR)) {
            mkdir(CAMERA_DIR, 0700, true);
        }

        if (!file_exists(ALERT_INI)) {
            $content = 'alert_enable = "no"' . PHP_EOL;
            $content .= 'monday = 00:00-23:59' . PHP_EOL;
            $content .= 'tuesday = 00:00-23:59' . PHP_EOL;
            $content .= 'wednesday = 00:00-23:59' . PHP_EOL;
            $content .= 'thursday = 00:00-23:59' . PHP_EOL;
            $content .= 'friday = 00:00-23:59' . PHP_EOL;
            $content .= 'saturday = 00:00-23:59' . PHP_EOL;
            $content .= 'sunday = 00:00-23:59' . PHP_EOL;

            file_put_contents(ALERT_INI, $content);
        }

        \Controllers\Autoloader::register();
    }
}
