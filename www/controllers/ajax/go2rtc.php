<?php
/**
 *  Get go2rtc log
 */
if ($_POST['action'] == 'get-log' and !empty($_POST['log'])) {
    $go2rtcController = new \Controllers\Go2rtc\Go2rtc();

    try {
        $content = $go2rtcController->getLog($_POST['log']);
    } catch (Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, $content);
}

response(HTTP_BAD_REQUEST, 'Invalid action');
