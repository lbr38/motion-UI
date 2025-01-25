<?php

namespace Controllers\Camera\Param;

use Exception;

class BasicAuthPassword
{
    /**
     *  Check that password is valid
     */
    public static function check(string $password) : void
    {
        /**
         *  Check that URL does not contain invalid characters
         */
        if (str_contains($password, "'") || str_contains($password, '"') || str_contains($password, '`') || str_contains($password, "\\") || str_contains($password, "/") || str_contains($password, '<') || str_contains($password, '>')) {
            throw new Exception('Password contains invalid characters');
        }
    }
}
