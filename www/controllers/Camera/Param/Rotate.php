<?php

namespace Controllers\Camera\Param;

use Exception;

class Rotate
{
    /**
     *  Check that rotate is valid
     */
    public static function check(string $rotate) : void
    {
        /**
         *  Check that rotate is not empty
         */
        if (!isset($rotate)) {
            throw new Exception('Rotate is required');
        }

        /**
         *  Check that rotate is valid
         */
        if (!is_numeric($rotate) or $rotate < 0 or $rotate > 270) {
            throw new Exception('Rotate value is invalid');
        }
    }
}
