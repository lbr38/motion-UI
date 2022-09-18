<?php

define("ROOT", dirname(__FILE__, 4));

const HTTP_OK = 200;
const HTTP_BAD_REQUEST = 400;
const HTTP_METHOD_NOT_ALLOWED = 405;

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {
    require_once(ROOT . "/controllers/Autoloader.php");
    \Controllers\Autoloader::load();

    if (!empty($_POST['action'])) {
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
