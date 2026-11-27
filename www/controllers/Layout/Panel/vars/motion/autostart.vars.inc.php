<?php
if (!IS_ADMIN) {
    throw new Exception(__('You are not allowed to access this panel.'));
}

$mymotionAutostart = new \Controllers\Motion\Autostart();

$autostartConfiguration = $mymotionAutostart->getConfiguration();
$autostartDevicePresenceEnabled = $mymotionAutostart->getDevicePresenceStatus();
$autostartKnownDevices = $mymotionAutostart->getDevices();
