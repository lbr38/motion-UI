<?php
/**
 *  Send a test email
 */
if ($_POST['action'] == "sendTestEmail" and !empty($_POST['mailRecipient'])) {
    try {
        new \Controllers\Mail($_POST['mailRecipient'], 'Test email', 'This is a test email sent by motion-UI.');
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Email sent');
}

/*
 *  Enable / disable alerts
 */
if ($_POST['action'] == "enableAlert" and !empty($_POST['status'])) {
    $mymotionAlert = new \Controllers\Motion\Alert();

    try {
        $mymotionAlert->enable($_POST['status']);
    } catch (Exception $e) {
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
    } catch (Exception $e) {
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
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, '');
}

/**
 *  Start / stop motion service
 */
if ($_POST['action'] == "start-stop" and !empty($_POST['status'])) {
    $mymotion = new \Controllers\Motion\Motion();

    try {
        $mymotion->startStop($_POST['status']);
    } catch (Exception $e) {
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
    } catch (Exception $e) {
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
    } catch (Exception $e) {
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
    } catch (Exception $e) {
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
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Settings saved');
}

/**
 *  Acquit all events
 */
if ($_POST['action'] == 'acquit-events') {
    $mymotionEvent = new \Controllers\Motion\Event();

    try {
        $mymotionEvent->acquitAll();
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'All events acquitted');
}

/**
 *  Get event image or video path to visualize
 */
if ($_POST['action'] == "getFilePath" and !empty($_POST['fileId'])) {
    $mymotion = new \Controllers\Motion\Motion();

    try {
        $symlinkName = $mymotion->getFilePath($_POST['fileId']);
    } catch (Exception $e) {
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
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Media file(s) successfully deleted');
}

/**
 *  Get event media total size for the specified date
 */
if ($_POST['action'] == 'get-event-date-total-media-size' and !empty($_POST['date'])) {
    try {
        $mymotionEvent = new \Controllers\Motion\Event();
        $size = $mymotionEvent->getTotalMediaSizeByDate($_POST['date']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, $size);
}

/**
 *  Configure motion
 */
if ($_POST['action'] == "configure-motion" and !empty($_POST['cameraId']) and !empty($_POST['params'])) {
    $mymotionConfig = new \Controllers\Motion\Config();

    try {
        $mymotionConfig->configure($_POST['cameraId'], $_POST['params']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Configuration saved.');
}

/**
 *  Get motion log
 */
if ($_POST['action'] == 'get-log' and !empty($_POST['log'])) {
    $motionServiceController = new \Controllers\Motion\Service();

    try {
        $content = $motionServiceController->getLog($_POST['log']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, $content);
}

response(HTTP_BAD_REQUEST, 'Invalid action');
