<?php

define("ROOT", dirname(__FILE__, 3));

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
            $myalert = new \Controllers\Alert();

            try {
                $myalert->enable($_POST['status']);
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
            $myalert = new \Controllers\Alert();

            try {
                $myalert->configure(
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
             *  Si il n'y a pas eu d'erreur
             */
            response(HTTP_OK, 'Settings saved');
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

            response(HTTP_OK, 'Configuration saved');
        }

        /**
         *  Add a new camera
         */
        if ($_POST['action'] == "addCamera" and !empty($_POST['cameraName']) and !empty($_POST['cameraUrl'])) {
            $mycamera = new \Controllers\Camera();

            try {
                $mycamera->add($_POST['cameraName'], $_POST['cameraUrl']);
            } catch (\Exception $e) {
                response(HTTP_BAD_REQUEST, $e->getMessage());
            }

            response(HTTP_OK, 'Camera added');
        }

        /**
         *  Edit camera configuration
         */
        if ($_POST['action'] == "editCamera" and isset($_POST['cameraId']) and isset($_POST['cameraName']) and isset($_POST['cameraUrl']) and isset($_POST['cameraRotate']) and isset($_POST['cameraRefresh'])) {
            $mycamera = new \Controllers\Camera();

            try {
                $mycamera->edit($_POST['cameraId'], $_POST['cameraName'], $_POST['cameraUrl'], $_POST['cameraRotate'], $_POST['cameraRefresh']);
            } catch (\Exception $e) {
                response(HTTP_BAD_REQUEST, $e->getMessage());
            }

            response(HTTP_OK, 'Camera <b>' . $_POST['cameraName'] . '</b> edited');
        }

        /**
         *  Delete camera
         */
        if ($_POST['action'] == "deleteCamera" and !empty($_POST['cameraId'])) {
            $mycamera = new \Controllers\Camera();

            try {
                $mycamera->delete($_POST['cameraId']);
            } catch (\Exception $e) {
                response(HTTP_BAD_REQUEST, $e->getMessage());
            }

            response(HTTP_OK, 'Camera deleted');
        }

        if ($_POST['action'] == "reloadImage" and !empty($_POST['cameraId'])) {
            $mycamera = new \Controllers\Camera();

            try {
                $mycamera->reloadImage($_POST['cameraId']);
            } catch (\Exception $e) {
                response(HTTP_BAD_REQUEST, $e->getMessage());
            }

            response(HTTP_OK, '');
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
