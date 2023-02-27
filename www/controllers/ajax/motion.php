<?php

/*
 *  Enable / disable alerts
 */
if ($_POST['action'] == "enableAlert" and !empty($_POST['status'])) {
    $mymotion = new \Controllers\Motion();

    try {
        $mymotion->enableAlert($_POST['status']);
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
    $mymotion = new \Controllers\Motion();

    try {
        $mymotion->configureAlert(
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
 *  Generate muttrc template file
 */
if ($_POST['action'] == "generateMuttrc") {
    $mymotion = new \Controllers\Motion();

    try {
        $mymotion->generateMuttrc();
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Muttrc template file has been generated');
}

/*
 *  Edit muttrc configuration file
 */
if ($_POST['action'] == "editMutt" and !empty($_POST['realName']) and !empty($_POST['from']) and !empty($_POST['smtpUrl']) and !empty($_POST['smtpPassword'])) {
    $mymotion = new \Controllers\Motion();

    try {
        $mymotion->editMutt($_POST['realName'], $_POST['from'], $_POST['smtpUrl'], $_POST['smtpPassword']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Mutt configuration has been saved');
}

/*
 *  Enable / disable autostart
 */
if ($_POST['action'] == "enableAutostart" and !empty($_POST['status'])) {
    $mymotion = new \Controllers\Motion();

    try {
        $mymotion->enableAutostart($_POST['status']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, '');
}

/**
 *  Start / stop motion capture
 */
if ($_POST['action'] == "startStopMotion" and !empty($_POST['status'])) {
    $mymotion = new \Controllers\Motion();

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
    $mymotion = new \Controllers\Motion();

    try {
        $mymotion->configureAutostart(
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
    $mymotion = new \Controllers\Motion();

    try {
        $mymotion->addDevice($_POST['name'], $_POST['ip']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Device added');
}

/**
 *  Remove a known device
 */
if ($_POST['action'] == "removeDevice" and !empty($_POST['id'])) {
    $mymotion = new \Controllers\Motion();

    try {
        $mymotion->removeDevice($_POST['id']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Device removed');
}

/**
 *  Enable / disable autostart on device presence
 */
if ($_POST['action'] == "enableDevicePresence" and !empty($_POST['status'])) {
    $mymotion = new \Controllers\Motion();

    try {
        $mymotion->enableDevicePresence($_POST['status']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Settings saved');
}

/**
 *  Get event image or video link to visualize
 */
if ($_POST['action'] == "getEventFile" and !empty($_POST['fileId'])) {
    $mymotion = new \Controllers\Motion();

    try {
        $symlinkName = $mymotion->getEventFile($_POST['fileId']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, $symlinkName);
}

/**
 *  Delete event media file
 */
if ($_POST['action'] == "deleteFile" and !empty($_POST['mediaId'])) {
    $mymotion = new \Controllers\Motion();

    try {
        $mymotion->deleteFile($_POST['mediaId']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Media file(s) successfully deleted');
}

/**
 *  Configure motion
 */
if ($_POST['action'] == "configureMotion" and !empty($_POST['cameraId']) and !empty($_POST['options_array'])) {
    $mymotion = new \Controllers\Motion();

    try {
        $mymotion->configure($_POST['cameraId'], $_POST['options_array']);
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, 'Configuration saved.');
}

response(HTTP_BAD_REQUEST, 'Invalid action');
