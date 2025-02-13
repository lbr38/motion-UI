<?php
if (!IS_ADMIN) {
    throw new Exception('You are not allowed to access this page.');
}

$mymotion = new \Controllers\Motion\Motion();
$mymotionService = new \Controllers\Motion\Service();
$mymotionEvent = new \Controllers\Motion\Event();
