<?php

namespace Controllers;

use Exception;

class System
{
    public static function getLoad()
    {
        $load = sys_getloadavg();
        $load = substr($load[0], 0, 4);

        return $load;
    }
}
