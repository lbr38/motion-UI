<?php

/**
 *  Edit global settings
 */
if ($_POST['action'] == "edit" and !empty($_POST['settings_params_json'])) {
    $mysettings = new \Controllers\Settings();

    try {
        $mysettings->edit($_POST['settings_params_json']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Settings saved.');
}

/**
 *  Enable / disable motion configuration's advanced edition mode
 */
if ($_POST['action'] == "advancedEditionMode" and !empty($_POST['status'])) {
    $mysettings = new \Controllers\Settings();

    try {
        $mysettings->motionAdvancedEditionMode($_POST['status']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Settings saved.');
}

response(HTTP_BAD_REQUEST, 'Invalid action');
