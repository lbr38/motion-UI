<?php

namespace Controllers\Camera\Param;

use Exception;

class BasicAuthUsername
{
    /**
     *  Check that username is valid
     */
    public static function check(string $username) : void
    {
        /**
         *  Check that URL does not contain invalid characters
         */
        if (str_contains($username, "'") || str_contains($username, '"') || str_contains($username, '`') || str_contains($username, "\\") || str_contains($username, '<') || str_contains($username, '>')) {
            throw new Exception('Username contains invalid characters');
        }
    }
}
