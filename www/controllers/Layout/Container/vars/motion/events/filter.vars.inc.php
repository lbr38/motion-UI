<?php
$cameraController = new \Controllers\Camera\Camera();

// Get all cameras names and Ids for the filter
$cameras = $cameraController->getNames();

unset($cameraController);
