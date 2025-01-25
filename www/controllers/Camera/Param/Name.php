<?php

namespace Controllers\Camera\Param;

use Exception;

class Name
{
    /**
     *  Check that name is valid
     */
    public static function check(string $name) : void
    {
        /**
         *  Check that name is not empty
         */
        if (empty($name)) {
            throw new Exception('Name is required');
        }

        /**
         *  Check that name is valid
         */
        if (!\Controllers\Common::isAlphanumDash($name, array(' '))) {
            throw new Exception('Name contains invalid characters');
        }
    }
}
