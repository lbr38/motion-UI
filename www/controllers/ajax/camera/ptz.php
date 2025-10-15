<?php
/**
 *  Move the camera
 */
if ($_POST['action'] == 'move' and !empty($_POST['cameraId']) and !empty($_POST['direction']) and !empty($_POST['moveType']) and !empty($_POST['moveSpeed'])) {
    try {
        $ptzMoveController = new \Controllers\Onvif\PtzMove($_POST['cameraId']);
        $ptzMoveController->move($_POST['direction'], $_POST['moveType'], $_POST['moveSpeed']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, '');
}

/**
 *  Stop the camera movement
 */
if ($_POST['action'] == 'stop' and !empty($_POST['cameraId'])) {
    try {
        $ptzMoveController = new \Controllers\Onvif\PtzMove($_POST['cameraId']);
        $ptzMoveController->stop();
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, '');
}

response(HTTP_BAD_REQUEST, 'Invalid action');
