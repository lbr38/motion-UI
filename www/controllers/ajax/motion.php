<?php

/*
 *  Enable / disable alerts
 */
if ($_POST['action'] == "enableAlert" and !empty($_POST['status'])) {
    $mymotionAlert = new \Controllers\Motion\Alert();

    try {
        $mymotionAlert->enable($_POST['status']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, '');
}

/*
 *  Configure alerts
 */
if ($_POST['action'] == "configureAlert"
    and isset($_POST['mondayStart'])
    and isset($_POST['mondayEnd'])
    and isset($_POST['tuesdayStart'])
    and isset($_POST['tuesdayEnd'])
    and isset($_POST['wednesdayStart'])
    and isset($_POST['wednesdayEnd'])
    and isset($_POST['thursdayStart'])
    and isset($_POST['thursdayEnd'])
    and isset($_POST['fridayStart'])
    and isset($_POST['fridayEnd'])
    and isset($_POST['saturdayStart'])
    and isset($_POST['saturdayEnd'])
    and isset($_POST['sundayStart'])
    and isset($_POST['sundayEnd'])
    and isset($_POST['mailRecipient'])) {
    $mymotionAlert = new \Controllers\Motion\Alert();

    try {
        $mymotionAlert->configure(
            $_POST['mondayStart'],
            $_POST['mondayEnd'],
            $_POST['tuesdayStart'],
            $_POST['tuesdayEnd'],
            $_POST['wednesdayStart'],
            $_POST['wednesdayEnd'],
            $_POST['thursdayStart'],
            $_POST['thursdayEnd'],
            $_POST['fridayStart'],
            $_POST['fridayEnd'],
            $_POST['saturdayStart'],
            $_POST['saturdayEnd'],
            $_POST['sundayStart'],
            $_POST['sundayEnd'],
            $_POST['mailRecipient']
        );
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Settings saved');
}

/*
 *  Enable / disable autostart
 */
if ($_POST['action'] == "enableAutostart" and !empty($_POST['status'])) {
    $mymotionAutostart = new \Controllers\Motion\Autostart();

    try {
        $mymotionAutostart->enable($_POST['status']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, '');
}

/**
 *  Start / stop motion capture
 */
if ($_POST['action'] == "startStopMotion" and !empty($_POST['status'])) {
    $mymotion = new \Controllers\Motion\Motion();

    try {
        $mymotion->startStop($_POST['status']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, '');
}

/*
 *  Configure motion autostart
 */
if ($_POST['action'] == "configureAutostart"
    and isset($_POST['mondayStart'])
    and isset($_POST['mondayEnd'])
    and isset($_POST['tuesdayStart'])
    and isset($_POST['tuesdayEnd'])
    and isset($_POST['wednesdayStart'])
    and isset($_POST['wednesdayEnd'])
    and isset($_POST['thursdayStart'])
    and isset($_POST['thursdayEnd'])
    and isset($_POST['fridayStart'])
    and isset($_POST['fridayEnd'])
    and isset($_POST['saturdayStart'])
    and isset($_POST['saturdayEnd'])
    and isset($_POST['sundayStart'])
    and isset($_POST['sundayEnd'])) {
    $mymotionAutostart = new \Controllers\Motion\Autostart();

    try {
        $mymotionAutostart->configure(
            $_POST['mondayStart'],
            $_POST['mondayEnd'],
            $_POST['tuesdayStart'],
            $_POST['tuesdayEnd'],
            $_POST['wednesdayStart'],
            $_POST['wednesdayEnd'],
            $_POST['thursdayStart'],
            $_POST['thursdayEnd'],
            $_POST['fridayStart'],
            $_POST['fridayEnd'],
            $_POST['saturdayStart'],
            $_POST['saturdayEnd'],
            $_POST['sundayStart'],
            $_POST['sundayEnd']
        );
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Settings saved');
}

/**
 *  Add a new device
 */
if ($_POST['action'] == "addDevice" and !empty($_POST['name']) and !empty($_POST['ip'])) {
    $mymotionDevice = new \Controllers\Motion\Device();

    try {
        $mymotionDevice->add($_POST['name'], $_POST['ip']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Device added');
}

/**
 *  Remove a known device
 */
if ($_POST['action'] == "removeDevice" and !empty($_POST['id'])) {
    $mymotionDevice = new \Controllers\Motion\Device();

    try {
        $mymotionDevice->remove($_POST['id']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Device removed');
}

/**
 *  Enable / disable autostart on device presence
 */
if ($_POST['action'] == "enableDevicePresence" and !empty($_POST['status'])) {
    $mymotionAutostart = new \Controllers\Motion\Autostart();

    try {
        $mymotionAutostart->enableDevicePresence($_POST['status']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Settings saved');
}

/**
 *  Get event image or video path to visualize
 */
if ($_POST['action'] == "getFilePath" and !empty($_POST['fileId'])) {
    $mymotion = new \Controllers\Motion\Motion();

    try {
        $symlinkName = $mymotion->getFilePath($_POST['fileId']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, $symlinkName);
}

/**
 *  Delete event media file
 */
if ($_POST['action'] == "deleteFile" and !empty($_POST['mediaId'])) {
    $mymotionEvent = new \Controllers\Motion\Event();

    try {
        $mymotionEvent->deleteFile($_POST['mediaId']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Media file(s) successfully deleted');
}

/**
 *  Configure motion
 */
if ($_POST['action'] == "configureMotion" and !empty($_POST['cameraId']) and !empty($_POST['options_array'])) {
    $mymotion = new \Controllers\Motion\Motion();

    try {
        $mymotion->configure($_POST['cameraId'], $_POST['options_array']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Configuration saved.');
}

response(HTTP_BAD_REQUEST, 'Invalid action');
