<?php

$mycamera = new \Controllers\Camera\Camera();

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
    !empty($_POST['timelapseEnable']) and
    isset($_POST['username']) and
    isset($_POST['password'])) {
    try {
        $mycamera->add($_POST['name'], $_POST['url'], $_POST['streamUrl'], $_POST['outputType'], $_POST['outputResolution'], $_POST['refresh'], $_POST['liveEnable'], $_POST['motionEnable'], $_POST['timelapseEnable'], $_POST['username'], $_POST['password']);
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
 *  Edit camera global settings
 */
if ($_POST['action'] == 'edit-global-settings' and
    !empty($_POST['id']) and
    !empty($_POST['name']) and
    !empty($_POST['url']) and
    isset($_POST['streamUrl']) and
    !empty($_POST['outputResolution']) and
    isset($_POST['rotate']) and
    isset($_POST['textLeft']) and
    isset($_POST['textRight']) and
    !empty($_POST['liveEnable']) and
    !empty($_POST['motionEnable']) and
    !empty($_POST['timelapseEnable']) and
    isset($_POST['username']) and
    isset($_POST['password'])) {
    try {
        $mycamera->editGlobalSettings($_POST['id'], $_POST['name'], $_POST['url'], $_POST['streamUrl'], $_POST['outputResolution'], $_POST['rotate'], $_POST['textLeft'], $_POST['textRight'], $_POST['liveEnable'], $_POST['motionEnable'], $_POST['timelapseEnable'], $_POST['username'], $_POST['password']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Camera global settings saved');
}

/**
 *  Edit camera stream settings
 */
if ($_POST['action'] == 'edit-stream-settings' and
    !empty($_POST['id']) and
    isset($_POST['refresh']) and
    isset($_POST['timestampLeft']) and
    isset($_POST['timestampRight'])) {
    try {
        $mycamera->editStreamSettings($_POST['id'], $_POST['refresh'], $_POST['timestampLeft'], $_POST['timestampRight']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Camera stream settings saved');
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
