<?php
$mycamera = new \Controllers\Camera\Camera();
$mymotionEvent = new \Controllers\Motion\Event();
$mypermission = new \Controllers\User\Permission();

/**
 *  Get total cameras and cameras Ids
 */
$cameraTotal = $mycamera->getTotal();
if ($cameraTotal > 0) {
    $cameraIds = $mycamera->getCamerasIds();
}

/**
 *  If the user is not an admin, get user permissions
 */
if (!IS_ADMIN) {
    $permissions = $mypermission->get($_SESSION['id']);
}
