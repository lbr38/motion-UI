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
        if (!is_dir(ROOT . '/db')) {
            mkdir(ROOT . '/db', 0770, true);
        }

        define('CAMERA_DIR', ROOT . '/configurations');

        /**
         *  Création des répertoires de base si n'existent pas
         */
        if (!is_dir(CAMERA_DIR)) {
            mkdir(CAMERA_DIR, 0700, true);
        }

        \Controllers\Autoloader::register();
    }
}
