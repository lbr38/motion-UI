<?php

$mycamera = new \Controllers\Camera\Camera();

/**
 *  Add a new camera
 */
if ($_POST['action'] == "add" and !empty($_POST['params'])) {
    try {
        $mycamera->add($_POST['params']);
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
        /**
         *  Get camera configuration
         */
        $camera = $mycamera->getConfiguration($_POST['id']);

        /**
         *  Generate configuration form for this camera
         */
        ob_start();
        include_once(ROOT . '/views/includes/camera/edit/form.inc.php');
        $form = ob_get_clean();
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, $form);
}

/**
 *  Edit camera global settings
 */
if ($_POST['action'] == 'edit-global-settings' and !empty($_POST['id']) and !empty($_POST['params'])) {
    try {
        $mycamera->editGlobalSettings($_POST['id'], $_POST['params']);
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
        $mycamera->delete($_POST['cameraId']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Camera deleted');
}

response(HTTP_BAD_REQUEST, 'Invalid action');
