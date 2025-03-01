<?php

namespace Controllers\Camera\Param;

use Exception;

class Framerate
{
    /**
     *  Check that frame rate is valid
     */
    public static function check(string $framerate) : void
    {
        /**
         *  Check that frame rate is not empty
         */
        if (!isset($framerate)) {
            throw new Exception('Frame rate is required');
        }

        /**
         *  Check that frame rate is valid
         */
        if (!is_numeric($framerate) or $framerate < 2) {
            throw new Exception('Frame rate value is invalid');
        }
    }
}
