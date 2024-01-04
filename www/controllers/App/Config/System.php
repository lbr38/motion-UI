<?php

namespace Controllers\App\Config;

class System
{
    /**
     *  Load system and OS informations
     */
    public static function get()
    {
        setlocale(LC_ALL, 'en_EN');

        /**
         *  Protocol (http ou https)
         */
        if (!defined('__SERVER_PROTOCOL__')) {
            if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                define('__SERVER_PROTOCOL__', 'https');
            } else {
                define('__SERVER_PROTOCOL__', 'http');
            }
        }

        /**
         *  Url du serveur
         */
        if (!empty($_SERVER['SERVER_NAME'])) {
            if (!defined('__SERVER_URL__')) {
                define('__SERVER_URL__', __SERVER_PROTOCOL__ . '://' . $_SERVER['HTTP_HOST']);
            }
        }

        /**
         *  Adresse IP du serveur
         */
        if (!empty($_SERVER['SERVER_ADDR'])) {
            if (!defined('__SERVER_IP__')) {
                define('__SERVER_IP__', $_SERVER['SERVER_ADDR']);
            }
        }
        /**
         *  URL + URI complètes
         */
        if (!empty($_SERVER['HTTP_HOST']) and !empty($_SERVER['REQUEST_URI'])) {
            if (!defined('__ACTUAL_URL__')) {
                define('__ACTUAL_URL__', __SERVER_PROTOCOL__ . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            }
        }

        /**
         *  URI
         */
        if (!defined('__ACTUAL_URI__')) {
            /**
             *  If sourceUri is set (POST request from ajax) then we use it
             */
            if (!empty($_POST['sourceUri'])) {
                define('__ACTUAL_URI__', explode('/', $_POST['sourceUri']));
            } else {
                if (!empty($_SERVER["REQUEST_URI"])) {
                    define('__ACTUAL_URI__', explode('/', parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH)));
                } else {
                    define('__ACTUAL_URI__', '');
                }
            }
        }

        /**
         *  Paramètres
         */
        if (!empty($_SERVER['QUERY_STRING'])) {
            if (!defined('__QUERY_STRING__')) {
                define('__QUERY_STRING__', parse_url($_SERVER["QUERY_STRING"], PHP_URL_PATH));
            }
        }
    }
}
