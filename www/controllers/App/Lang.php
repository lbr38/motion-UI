<?php

namespace Controllers\App;

use Controllers\App\Lang\PoFileLoader;
use Symfony\Component\Translation\Translator;

class Lang
{
    private static $translator;

    /**
     *  Return available languages: English (source language) and every language having a translation file
     */
    public static function available(): array
    {
        $languages = ['en'];

        // translations/messages.<lang>.po
        foreach (glob(ROOT . '/translations/messages.*.po') as $file) {
            $languages[] = explode('.', basename($file))[1];
        }

        return $languages;
    }

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

            if (in_array($code, self::available(), true)) {
                return $code;
            }
        }

        return $default;
    }

    /**
     *  Return the current language
     */
    public static function get(): string
    {
        return self::detectBrowserLanguage();
    }

    /**
     *  Return all translations of a domain for the current language
     *  Used to provide translations to the javascript code
     */
    public static function catalogue(string $domain): array
    {
        return self::translator()->getCatalogue()->all($domain);
    }

    /**
     *  Translate a string, with an optional context to disambiguate identical strings
     *  Return the source string if no translation is found
     */
    public static function translate(string $text, string|null $context = null): string
    {
        // Same convention as gettext: context and string are separated by an EOT character
        $id = $context === null ? $text : $context . "\x04" . $text;

        $translation = self::translator()->trans($id);

        // Symfony returns the id when the string is not translated
        if ($translation === $id) {
            return $text;
        }

        return $translation;
    }

    /**
     *  Translate a string with plural forms
     *  Return the source singular or plural string if no translation is found
     */
    public static function translatePlural(string $singular, string $plural, int $count): string
    {
        // Plural strings are stored as 'singular|plural' by the PO loader
        $id = $singular . '|' . $plural;

        if (!self::translator()->getCatalogue()->has($id)) {
            return $count == 1 ? $singular : $plural;
        }

        // Symfony selects the plural form matching the count and the current language rules
        return self::translator()->trans($id, ['%count%' => $count]);
    }

    /**
     *  Initialize the translator for the current language (once per request)
     */
    private static function translator(): Translator
    {
        if (isset(self::$translator)) {
            return self::$translator;
        }

        require_once(ROOT . '/libs/vendor/autoload.php');

        $lang = self::get();

        self::$translator = new Translator($lang);
        self::$translator->addLoader('po', new PoFileLoader());

        /**
         *  English is the source language, it has no translation file
         *  Each domain has its own file: messages (PHP code) and javascript (JS code)
         */
        if ($lang != 'en') {
            foreach (['messages', 'javascript'] as $domain) {
                if (file_exists(ROOT . '/translations/' . $domain . '.' . $lang . '.po')) {
                    self::$translator->addResource('po', ROOT . '/translations/' . $domain . '.' . $lang . '.po', $lang, $domain);
                }
            }
        }

        return self::$translator;
    }
}
