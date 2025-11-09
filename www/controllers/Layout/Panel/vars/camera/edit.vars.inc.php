<?php
if (!IS_ADMIN) {
    throw new Exception('You are not allowed to access this panel.');
}

$mycamera = new \Controllers\Camera\Camera();
$userController = new \Controllers\User\User();
$cameraId = $item['id'];
$onvifFieldsClass = '';

/**
 *  Get camera configuration
 */
$camera = $mycamera->getConfiguration($cameraId);

/**
 *  Get all users emails
 */
$usersEmails = $userController->getEmails();

/**
 *  Get cameras raw params
 */
try {
    $cameraRawParams = json_decode($camera['Configuration'], true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    throw new Exception('Error: could not retrieve camera #' . $cameraId . ' configuration: ' . $e->getMessage());
}

/**
 *  If ONVIF is not enabled, hide ONVIF fields
 */
if (empty($cameraRawParams['onvif']['enable']) or $cameraRawParams['onvif']['enable'] != "true") {
    $onvifFieldsClass = 'hide';
}

unset($mycamera, $userController);
