<?php

namespace Controllers\Camera\Param;

use Exception;

class OnvifUri
{
    /**
     *  Check that URI is valid
     */
    public static function check(string $uri) : void
    {
        if (empty($uri)) {
            return;
        }

        /**
         *  Check that URI is valid
         *  Should start with '/' or '?' and contain only allowed characters
         */
        if (!preg_match('/^[\?\/a-zA-Z0-9\-\_\&\=\%]+$/', $uri)) {
            throw new Exception('Invalid ONVIF URI');
        }
    }
}
