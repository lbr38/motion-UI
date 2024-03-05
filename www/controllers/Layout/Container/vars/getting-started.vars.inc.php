<?php

$mycamera = new \Controllers\Camera\Camera();

/**
 *  Get total cameras and cameras Ids
 */
$cameraTotal = $mycamera->getTotal();
if ($cameraTotal > 0) {
    $cameraIds = $mycamera->getCamerasIds();
}
