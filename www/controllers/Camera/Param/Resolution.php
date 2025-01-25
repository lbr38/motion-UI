<?php

namespace Controllers\Camera\Param;

use Exception;

class Resolution
{
    /**
     *  Check that resolution is valid
     */
    public static function check(string $resolution) : void
    {
        /**
         *  Check that resolution is not empty
         */
        if (empty($resolution)) {
            throw new Exception('Resolution is required');
        }

        /**
         *  Check that resolution is valid
         */
        if (!preg_match('#^([0-9]+)x([0-9]+)$#', $resolution)) {
            throw new Exception('Specified resolution is invalid');
        }
    }
}
