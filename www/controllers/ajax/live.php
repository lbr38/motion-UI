<?php

/**
 *  Add a new camera
 */
if ($_POST['action'] == "addCamera" and !empty($_POST['cameraName']) and !empty($_POST['cameraUrl'])) {
    $mycamera = new \Controllers\Camera();

    try {
        $mycamera->add($_POST['cameraName'], $_POST['cameraUrl']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Camera added');
}

/**
 *  Edit camera configuration
 */
if ($_POST['action'] == "editCamera" and isset($_POST['cameraId']) and isset($_POST['cameraName']) and isset($_POST['cameraUrl']) and isset($_POST['cameraRotate']) and isset($_POST['cameraRefresh'])) {
    $mycamera = new \Controllers\Camera();

    try {
        $mycamera->edit($_POST['cameraId'], $_POST['cameraName'], $_POST['cameraUrl'], $_POST['cameraRotate'], $_POST['cameraRefresh']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Camera <b>' . $_POST['cameraName'] . '</b> edited');
}

/**
 *  Delete camera
 */
if ($_POST['action'] == "deleteCamera" and !empty($_POST['cameraId'])) {
    $mycamera = new \Controllers\Camera();

    try {
        $mycamera->delete($_POST['cameraId']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Camera deleted');
}

if ($_POST['action'] == "reloadImage" and !empty($_POST['cameraId'])) {
    $mycamera = new \Controllers\Camera();

    try {
        $mycamera->reloadImage($_POST['cameraId']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, '');
}

response(HTTP_BAD_REQUEST, 'Invalid action');
