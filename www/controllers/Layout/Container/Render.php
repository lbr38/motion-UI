<?php

namespace Controllers\Layout\Container;

use Controllers\App\Lang;
use Exception;

class Render
{
    public static function render(string $container)
    {
        try {
            /**
             *  Check if container exists
             */
            if (!file_exists(ROOT . '/views/includes/containers/' . $container . '.inc.php')) {
                throw new Exception('Could not retrieve content: unknown container ' . $container);
            }

            // Include language file if exists
            if (file_exists(ROOT . '/controllers/Layout/Container/lang/' . $container . '.' . Lang::detectBrowserLanguage() . '.inc.php')) {
                include_once(ROOT . '/controllers/Layout/Container/lang/' . $container . '.' . Lang::detectBrowserLanguage() . '.inc.php');
            } else if (file_exists(ROOT . '/controllers/Layout/Container/lang/' . $container . '.en.inc.php')) {
                // Fallback to English if specific language file is not found
                include_once(ROOT . '/controllers/Layout/Container/lang/' . $container . '.en.inc.php');
            }

            /**
             *  Include vars file if exists
             */
            if (file_exists(ROOT . '/controllers/Layout/Container/vars/' . $container . '.vars.inc.php')) {
                include_once(ROOT . '/controllers/Layout/Container/vars/' . $container . '.vars.inc.php');
            }

            /**
             *  Include container content
             */
            include_once(ROOT . '/views/includes/containers/' . $container . '.inc.php');
        } catch (Exception $e) {
            /**
             *  Include error container
             */
            include(ROOT . '/views/includes/containers/error.inc.php');
        }
    }
}
