<?php
$mypermission = new \Controllers\User\Permission();
$mycamera = new \Controllers\Camera\Camera();

if (!IS_ADMIN) {
    throw new Exception('You are not allowed to access this panel.');
}

if (!isset($item['Id'])) {
    throw new Exception('User Id not set.');
}

$userId = $item['Id'];

/**
 *  Get user permissions
 */
$permissions = $mypermission->get($userId);

/**
 *  Get all cameras
 */
$cameras = $mycamera->get();
