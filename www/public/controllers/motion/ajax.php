<?php

define("ROOT", dirname(__FILE__, 4));

const HTTP_OK = 200;
const HTTP_BAD_REQUEST = 400;
const HTTP_METHOD_NOT_ALLOWED = 405;

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
    require_once(ROOT . "/controllers/Autoloader.php");
    \Controllers\Autoloader::load();

    if (!empty($_POST['action'])) {
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
            and isset($_POST['sundayEnd'])) {
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
                    $_POST['mailRecipient'],
                    $_POST['muttConfig']
                );
            } catch (\Exception $e) {
                response(HTTP_BAD_REQUEST, $e->getMessage());
            }

            /**
             *  If there was no error
             */
            response(HTTP_OK, 'Settings saved');
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

            /**
             *  If there was no error
             */
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
         *  Configure motion
         */
        if ($_POST['action'] == "configureMotion" and !empty($_POST['filename']) and !empty($_POST['options_array'])) {
            $mymotion = new \Controllers\Motion();

            try {
                $mymotion->configure($_POST['filename'], $_POST['options_array']);
            } catch (\Exception $e) {
                response(HTTP_BAD_REQUEST, $e->getMessage());
            }

            response(HTTP_OK, 'Configuration saved, please restart <b>motion</b> to apply it.');
        }

        /**
         *  Duplicate motion configuration file
         */
        if ($_POST['action'] == "duplicateConf" and !empty($_POST['filename'])) {
            $mymotion = new \Controllers\Motion();

            try {
                $mymotion->duplicateConf($_POST['filename']);
            } catch (\Exception $e) {
                response(HTTP_BAD_REQUEST, $e->getMessage());
            }

            response(HTTP_OK, 'Configuration file duplicated.');
        }

        /**
         *  Delete motion configuration file
         */
        if ($_POST['action'] == "deleteConf" and !empty($_POST['filename'])) {
            $mymotion = new \Controllers\Motion();

            try {
                $mymotion->deleteConf($_POST['filename']);
            } catch (\Exception $e) {
                response(HTTP_BAD_REQUEST, $e->getMessage());
            }

            response(HTTP_OK, 'Configuration file deleted, please restart <b>motion</b> to apply.');
        }

        /**
         *  Rename motion configuration file
         */
        if ($_POST['action'] == "renameConf" and !empty($_POST['filename']) and !empty($_POST['newName'])) {
            $mymotion = new \Controllers\Motion();

            try {
                $mymotion->renameConf($_POST['filename'], $_POST['newName']);
            } catch (\Exception $e) {
                response(HTTP_BAD_REQUEST, $e->getMessage());
            }

            response(HTTP_OK, 'Configuration file renamed to ' . $_POST['newName']);
        }

        /**
         *  If action doesn't match any action above
         */
        response(HTTP_BAD_REQUEST, 'Invalid action');
    }

    response(HTTP_BAD_REQUEST, 'Missing parameter');
} else {
    response(HTTP_METHOD_NOT_ALLOWED, 'Method not allowed');
}

function response($response_code, $message)
{
    header('Content-Type: application/json');
    http_response_code($response_code);

    $response = [
        "response_code" => $response_code,
        "message" => $message
    ];

    echo json_encode($response);

    exit;
}
