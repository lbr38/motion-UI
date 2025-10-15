<?php

namespace Controllers\Camera\Param;

use Exception;

class MonitoringEnable
{
    /**
     *  Check that monitoring enable is valid
     */
    public static function check(string $enable) : void
    {
        if ($enable != "true" and $enable != "false") {
            throw new Exception('Monitoring enable is invalid');
        }
    }
}
