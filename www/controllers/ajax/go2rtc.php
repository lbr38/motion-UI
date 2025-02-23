<?php
/**
 *  Get go2rtc log
 */
if ($_POST['action'] == 'get-log') {
    $go2rtcController = new \Controllers\Go2rtc\Go2rtc();

    try {
        $content = $go2rtcController->getLog();
    } catch (\Exception $e) {
        response(HTTP_BAD_REQUEST, $e->getMessage());
    }

    response(HTTP_OK, $content);
}

response(HTTP_BAD_REQUEST, 'Invalid action');
