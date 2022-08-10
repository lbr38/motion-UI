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
         *  Edit global settings
         */
        if ($_POST['action'] == "edit" and !empty($_POST['settings_params_json'])) {
            $mysettings = new \Controllers\Settings();

            try {
                $mysettings->edit($_POST['settings_params_json']);
            } catch (\Exception $e) {
                response(HTTP_BAD_REQUEST, $e->getMessage());
            }

            response(HTTP_OK, 'Settings saved.');
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
