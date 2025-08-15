<?php
$mycamera = new \Controllers\Camera\Camera();
$cameraStreamController = new \Controllers\Camera\Stream();
$mymotionService = new \Controllers\Motion\Service();
$mymotionEvent = new \Controllers\Motion\Event();
$mypermission = new \Controllers\User\Permission();

/**
 *  Get total cameras and cameras Ids
 */
$cameraTotal = $mycamera->getTotal();

/**
 *  If there are cameras, get their IDs and the cameras grid order
 */
if ($cameraTotal > 0) {
    // Get cameras IDs
    $camerasIds = $mycamera->getCamerasIds();

    // Get cameras grid order
    $camerasOrder = $cameraStreamController->getOrder();

    // Merge the two arrays to make sure all the cameras IDs are in the grid order
    $camerasOrder = array_unique(array_merge($camerasOrder, $camerasIds));
}

/**
 *  If the user is not an admin, get user permissions
 */
if (!IS_ADMIN) {
    $permissions = $mypermission->get($_SESSION['id']);
}

/**
 *  Get motion detection status
 */
$motionRunning = $mymotionService->isRunning();

unset($mymotionService, $mypermission);
