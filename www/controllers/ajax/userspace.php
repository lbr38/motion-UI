<?php
/**
 *  Change user password
 */
if ($_POST['action'] == "change-password" and !empty($_POST['id']) and !empty($_POST['currentPassword']) and !empty($_POST['newPassword']) and !empty($_POST['newPasswordRetype'])) {
    $myuser = new \Controllers\User\User();

    try {
        $myuser->changePassword($_POST['id'], $_POST['currentPassword'], $_POST['newPassword'], $_POST['newPasswordRetype']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Password changed.');
}

/**
 *  Create a new user
 */
if ($action == 'create-user' and !empty($_POST['username']) and !empty($_POST['role'])) {
    $myuser = new \Controllers\User\User();

    try {
        $generatedPassword = $myuser->add($_POST['username'], $_POST['role']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, array('message' => 'User <b>' . $_POST['username'] . '</b> has been created', 'password' => $generatedPassword));
}

/**
 *  Reset user password
 */
if ($action == 'reset-password' and !empty($_POST['id'])) {
    $myuser = new \Controllers\User\User();

    try {
        $generatedPassword = $myuser->resetPassword($_POST['id']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, array('message' => 'Password has been regenerated', 'password' => $generatedPassword));
}

/**
 *  Delete user
 */
if ($action == 'delete-user' and !empty($_POST['id'])) {
    $myuser = new \Controllers\User\User();

    try {
        $myuser->delete($_POST['id']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'User has been deleted');
}

response(HTTP_BAD_REQUEST, 'Invalid action');
