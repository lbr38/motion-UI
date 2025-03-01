<?php

$mycamera = new \Controllers\Camera\Camera();

/**
 *  Add a new camera
 */
if ($_POST['action'] == "add" and !empty($_POST['params'])) {
    try {
        $cameraAddController = new \Controllers\Camera\Add();
        $cameraAddController->add($_POST['params']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Camera added');
}

/**
 *  Edit camera global settings
 */
if ($_POST['action'] == 'edit-global-settings' and !empty($_POST['id']) and !empty($_POST['params'])) {
    try {
        $cameraEditController = new \Controllers\Camera\Edit();
        $cameraEditController->edit($_POST['id'], $_POST['params']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Camera settings saved');
}

/**
 *  Delete camera
 */
if ($_POST['action'] == "delete" and !empty($_POST['cameraId'])) {
    try {
        $cameraDeleteController = new \Controllers\Camera\Delete();
        $cameraDeleteController->delete($_POST['cameraId']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Camera deleted');
}

response(HTTP_BAD_REQUEST, 'Invalid action');
