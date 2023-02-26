<?php

$mycamera = new \Controllers\Camera();

/**
 *  Add a new camera
 */
if ($_POST['action'] == "add" and
    !empty($_POST['name']) and
    !empty($_POST['url']) and
    isset($_POST['streamUrl']) and
    !empty($_POST['outputType']) and
    !empty($_POST['outputResolution']) and
    isset($_POST['refresh']) and
    !empty($_POST['liveEnable']) and
    !empty($_POST['motionEnable']) and
    isset($_POST['username']) and
    isset($_POST['password'])) {
    try {
        $mycamera->add($_POST['name'], $_POST['url'], $_POST['streamUrl'], $_POST['outputType'], $_POST['outputResolution'], $_POST['refresh'], $_POST['liveEnable'], $_POST['motionEnable'], $_POST['username'], $_POST['password']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Camera added');
}

/**
 *  Get camera configuration edit form
 */
if ($_POST['action'] == "getEditForm" and !empty($_POST['id'])) {
    try {
        $mysettings = new \Controllers\Settings();
        $settings = $mysettings->get();

        /**
         *  Get camera configuration
         */
        $camera = $mycamera->getConfiguration($_POST['id']);

        /**
         *  Generate configuration form for this camera
         */
        ob_start();
        include_once(ROOT . '/views/includes/camera/edit-form.inc.php');
        $form = ob_get_clean();
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, $form);
}

/**
 *  Edit camera configuration
 */
if ($_POST['action'] == "edit" and
    !empty($_POST['id']) and
    !empty($_POST['name']) and
    !empty($_POST['url']) and
    isset($_POST['streamUrl']) and
    !empty($_POST['outputResolution']) and
    isset($_POST['refresh']) and
    isset($_POST['rotate']) and
    !empty($_POST['liveEnable']) and
    !empty($_POST['motionEnable']) and
    isset($_POST['username']) and
    isset($_POST['password'])) {
    try {
        $mycamera->edit($_POST['id'], $_POST['name'], $_POST['url'], $_POST['streamUrl'], $_POST['outputResolution'], $_POST['refresh'], $_POST['rotate'], $_POST['liveEnable'], $_POST['motionEnable'], $_POST['username'], $_POST['password']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Camera <b>' . $_POST['name'] . '</b> edited');
}

/**
 *  Delete camera
 */
if ($_POST['action'] == "delete" and !empty($_POST['cameraId'])) {
    try {
        $mycamera->delete($_POST['cameraId']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Camera deleted');
}

response(HTTP_BAD_REQUEST, 'Invalid action');
