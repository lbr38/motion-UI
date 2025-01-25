<?php
if (!IS_ADMIN) {
    throw new Exception('You are not allowed to access this panel.');
}

$mymotionAutostart = new \Controllers\Motion\Autostart();

$autostartConfiguration = $mymotionAutostart->getConfiguration();
$autostartDevicePresenceEnabled = $mymotionAutostart->getDevicePresenceStatus();
$autostartKnownDevices = $mymotionAutostart->getDevices();
