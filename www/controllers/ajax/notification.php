<?php

/**
 *  Mark a notification as read
 */
if ($action == "acquit" and !empty($_POST['id'])) {
    $mynotification = new \Controllers\Notification();

    try {
        $mynotification->acquit($_POST['id']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, __('Notification marked as read'));
}

response(HTTP_BAD_REQUEST, __('Invalid action'));
