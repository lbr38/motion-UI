<?php

namespace Controllers\Layout\Panel\Motion;

class Autostart
{
    public static function render()
    {
        $mymotionAutostart = new \Controllers\Motion\Autostart();

        $autostartConfiguration = $mymotionAutostart->getConfiguration();
        $autostartDevicePresenceEnabled = $mymotionAutostart->getDevicePresenceStatus();
        $autostartKnownDevices = $mymotionAutostart->getDevices();

        include_once(ROOT . '/views/includes/panels/motion/autostart.inc.php');
    }
}
