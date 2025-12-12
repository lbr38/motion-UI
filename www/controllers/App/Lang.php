<?php

namespace Controllers\App;

use Exception;

class Lang
{
    /**
     *  Load proper lang file based on browser language
     */
    public static function load() : void
    {
        // TODO: to finish
        // $config = yaml_parse_file(ROOT . '/config/lang/' . self::detectBrowserLanguage() . '.yml');
        $config = yaml_parse_file(ROOT . '/config/lang/en.yml');

        if ($config === false) {
            throw new Exception('Failed to parse locale configuration file');
        }

        // Define a cosntant array with all locale strings
        if (!defined('LC')) {
            define('LC', $config);
        }
    }

    /**
     *  Detect the browser language from the HTTP_ACCEPT_LANGUAGE header
     */
    private static function detectBrowserLanguage(): string
    {
        $default = 'en';

        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return $default;
        }

        $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        foreach ($languages as $lang) {
            // fr-FR → fr
            $code = strtolower(substr(trim($lang), 0, 2));

            if (in_array($code, ['en', 'fr'], true)) {
                return $code;
            }
        }

        return $default;
    }
}
