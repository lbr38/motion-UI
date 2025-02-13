<?php
/**
 *  Grant camera access to user
 */
if ($action == 'cameras-access' and !empty($_POST['id']) and isset($_POST['cameras'])) {
    $mypermission = new \Controllers\User\Permission();

    try {
        $mypermission->grantCameraAccess($_POST['id'], $_POST['cameras']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Permissions saved');
}

response(HTTP_BAD_REQUEST, 'Invalid action: ' . $_POST['cameras']);
