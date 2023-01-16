<?php

/**
 *  Change user password
 */
if ($_POST['action'] == "changePassword" and !empty($_POST['username']) and !empty($_POST['currentPassword']) and !empty($_POST['newPassword']) and !empty($_POST['newPasswordRetype'])) {
    $mylogin = new \Controllers\Login();

    try {
        $mylogin->changePassword($_POST['username'], $_POST['currentPassword'], $_POST['newPassword'], $_POST['newPasswordRetype']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Password changed.');
}

response(HTTP_BAD_REQUEST, 'Invalid action');
