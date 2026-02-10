<?php

namespace Controllers\App;

class Lang
{
    /**
     *  Detect the browser language from the HTTP_ACCEPT_LANGUAGE header
     */
    public static function detectBrowserLanguage(): string
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
