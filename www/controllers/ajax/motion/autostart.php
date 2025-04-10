<?php
/**
 *  Get motion autostart log
 */
if ($_POST['action'] == 'get-log') {
    $motionAutostartController = new \Controllers\Motion\Autostart();

    try {
        response(HTTP_OK, $motionAutostartController->getLog());
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }
}

response(HTTP_BAD_REQUEST, 'Invalid action');
