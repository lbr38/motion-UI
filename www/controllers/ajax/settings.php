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

response(HTTP_BAD_REQUEST, 'Invalid action');
