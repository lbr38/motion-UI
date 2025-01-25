<?php

namespace Controllers\Camera\Param;

use Exception;

class TextLeft
{
    /**
     *  Check that text left is valid
     */
    public static function check(string $text) : void
    {
        /**
         *  Check that rotate is valid
         */
        if (!is_numeric($rotate) or $rotate < 0 or $rotate > 270) {
            throw new Exception('Rotate value is invalid');
        }
    }
}
