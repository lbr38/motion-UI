<?php

namespace Controllers\Camera\Param;

use Exception;

class OnvifEnable
{
    /**
     *  Check that Onvif enable is valid
     */
    public static function check(string $enable): void
    {
        if ($enable != "true" and $enable != "false") {
            throw new Exception(__('Onvif enable is invalid'));
        }
    }
}
