<?php
$systemCpuController = new \Controllers\System\Cpu();
$datasets = [];
$labels = [];
$options = [];

/**
 *  Get CPU usage data
 *  This will fetch the last 24 hours of CPU usage data
 */
$cpuUsageStats = $systemCpuController->get();

foreach ($cpuUsageStats as $stat) {
    // Convert timestamp to a human-readable format using Datetime
    $labels[] = (new DateTime())->setTimestamp($stat['Timestamp'])->format('H:i:s');
    $datasets[0]['data'][] = $stat['Cpu_usage'];
}

/**
 *  Add current CPU usage to the list
 */
$labels[] = date('H:i:s');
$datasets[0]['data'][] = \Controllers\System\Cpu::getUsage();

/**
 *  Prepare chart data
 */
$options['title']['text'] = 'CPU usage (%) (last hour)';
$options['legend']['display']['position'] = 'bottom';

$datasets[0]['backgroundColor'] = 'rgba(243, 47, 99, 0.20)';
$datasets[0]['borderColor'] = '#F32F63';
$datasets[0]['label'] = 'CPU usage';

unset($systemCpuController, $cpuUsageStats, $stat);
