<?php
$systemMonitoringController = new \Controllers\System\Monitoring\Monitoring();
$datasets = [];
$labels = [];
$options = [];

/**
 *  Get CPU usage data
 *  This will fetch the last 24 hours of CPU usage data
 */
$memoryUsageStats = $systemMonitoringController->get(time() - 3600, time());

foreach ($memoryUsageStats as $stat) {
    // Convert timestamp to a human-readable format using Datetime
    $labels[] = (new DateTime())->setTimestamp($stat['Timestamp'])->format('H:i:s');
    $datasets[0]['data'][] = $stat['Memory_usage'];
}

/**
 *  Add current memory usage to the list
 */
$labels[] = date('H:i:s');
$datasets[0]['data'][] = \Controllers\System\Monitoring\Memory::getUsage();

/**
 *  Prepare chart data
 */
$options['title']['text'] = 'Memory (RAM) usage (%) (last hour)';
$options['legend']['display']['position'] = 'bottom';

$datasets[0]['backgroundColor'] = 'rgba(243, 47, 99, 0.20)';
$datasets[0]['borderColor'] = '#F32F63';
$datasets[0]['label'] = 'Memory usage';

unset($systemMonitoringController, $memoryUsageStats, $stat);
