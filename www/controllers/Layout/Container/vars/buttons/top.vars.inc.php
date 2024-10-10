<?php
$mymotionService = new \Controllers\Motion\Service();

$load = \Controllers\System::getLoad();

if (!$mymotionService->isRunning()) {
    $serviceStatus = 'stopped';
} else {
    $serviceStatus = 'running';
}

unset($mymotionService);
