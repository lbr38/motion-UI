<?php

$mytimelapse = new \Controllers\Camera\Timelapse();

/**
 *  Get camera timelapse
 */
if ($_POST['action'] == 'get-timelapse' and !empty($_POST['cameraId'])) {
    try {
        $timelapse = $mytimelapse->display($_POST['cameraId']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    /**
     *  Return timelapse section
     */
    response(HTTP_OK, $timelapse);
}

/**
 *  Get timelapse
 */
if ($_POST['action'] == 'get-timelapse-by-date' and !empty($_POST['cameraId']) and !empty($_POST['date'])) {
    try {
        $timelapse = $mytimelapse->display($_POST['cameraId'], $_POST['date']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    /**
     *  Return timelapse section
     */
    response(HTTP_OK, $timelapse);
}

/**
 *  Get timelapse picture
 */
if ($_POST['action'] == 'get-timelapse-by-picture' and !empty($_POST['cameraId']) and !empty($_POST['date']) and !empty($_POST['picture'])) {
    try {
        $timelapse = $mytimelapse->display($_POST['cameraId'], $_POST['date'], $_POST['picture']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    /**
     *  Return timelapse section
     */
    response(HTTP_OK, $timelapse);
}

response(HTTP_BAD_REQUEST, 'Invalid action');
