<?php
if (!IS_ADMIN) {
    throw new Exception('You are not allowed to access this page.');
}

$mycamera = new \Controllers\Camera\Camera();
$mymotionService = new \Controllers\Motion\Service();
$mymotionAutostart = new \Controllers\Motion\Autostart();
$mymotionAlert = new \Controllers\Motion\Alert();

/**
 *  Get motion service status
 */
$motionActive = $mymotionService->isRunning();

/**
 *  Get autostart and alert settings
 */
$motionAutostartEnabled = $mymotionAutostart->getStatus();

/**
 *  Get total cameras and cameras Ids
 */
$cameraTotal = $mycamera->getTotal();

if ($cameraTotal > 0) {
    $cameraIds = $mycamera->getCamerasIds();
}
