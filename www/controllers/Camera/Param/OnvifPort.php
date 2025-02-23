<?php

namespace Controllers\Camera\Param;

use Exception;

class OnvifPort
{
    /**
     *  Check that port is valid
     */
    public static function check(string $port) : void
    {
        if (empty($port)) {
            return;
        }

        if ($port < 0 or $port > 65535) {
            throw new Exception('Onvif port is invalid');
        }
    }
}
