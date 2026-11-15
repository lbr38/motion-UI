<?php
/**
 *  Edit user personnal informations
 */
if ($_POST['action'] == 'edit' and isset($_POST['firstName']) and isset($_POST['lastName']) and isset($_POST['email'])) {
    try {
        $userEditController = new \Controllers\User\Edit();
        $userEditController->edit($_SESSION['id'], $_POST['firstName'], $_POST['lastName'], $_POST['email']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, __('Personnal informations saved'));
}

/**
 *  Change user password
 */
if ($_POST['action'] == "change-password" and !empty($_POST['id']) and !empty($_POST['currentPassword']) and !empty($_POST['newPassword']) and !empty($_POST['newPasswordRetype'])) {
    try {
        $userEditController = new \Controllers\User\Edit();
        $userEditController->changePassword($_POST['id'], $_POST['currentPassword'], $_POST['newPassword'], $_POST['newPasswordRetype']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, __('Password changed.'));
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

    response(HTTP_OK, array('message' => sprintf(__('User %s has been created'), '<b>' . $_POST['username'] . '</b>'), 'password' => $generatedPassword));
}

/**
 *  Reset user password
 */
if ($action == 'reset-password' and !empty($_POST['id'])) {
    try {
        $userEditController = new \Controllers\User\Edit();
        $generatedPassword = $userEditController->resetPassword($_POST['id']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, array('message' => __('Password has been regenerated'), 'password' => $generatedPassword));
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

    response(HTTP_OK, __('User has been deleted'));
}

response(HTTP_BAD_REQUEST, __('Invalid action'));
