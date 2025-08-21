<?php
$cameraStreamController = new \Controllers\Camera\Stream();

/**
 *  Enable or disable the camera stream
 */
if ($_POST['action'] == 'enable' and !empty($_POST['id']) and !empty($_POST['enable'])) {
    try {
        $cameraStreamController->enable($_POST['id'], $_POST['enable']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, '');
}

/**
 *  Sort the camera grid
 */
if ($_POST['action'] == 'sort' and !empty($_POST['order'])) {
    try {
        $cameraStreamController->sort($_POST['order']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, '');
}

response(HTTP_BAD_REQUEST, 'Invalid action');
