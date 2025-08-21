<?php
$mymotionService = new \Controllers\Motion\Service();

if (!$mymotionService->isRunning()) {
    $serviceStatus = 'stopped';
} else {
    $serviceStatus = 'running';
}

unset($mymotionService);
