<?php
$motionServiceController = new \Controllers\Motion\Service();
$labels = [];
$datasets = [];
$options = [];

/**
 *  The motion start and stop chart will print data on 24 hours
 */
$motionDailyStatus = $motionServiceController->getMotionServiceStatusStats();

foreach ($motionDailyStatus as $motionStatus) {
    $labels[] = $motionStatus['Time'];

    if ($motionStatus['Status'] == 'inactive') {
        $datasets[0]['data'][] = '0';
    }
    if ($motionStatus['Status'] == 'active') {
        $datasets[0]['data'][] = '1';
    }
}

/**
 *  Then add current motion status to the list
 */
$labels[] = date('H:i:s');

if ($motionServiceController->isRunning()) {
    $datasets[0]['data'][] = '1';
} else {
    $datasets[0]['data'][] = '0';
}

/**
 *  Prepare chart data
 */
$options['title']['text'] = 'Motion detection activity (24h)';
$options['legend']['display']['position'] = 'bottom';

// Callback for tooltip (JS function)
$options['tooltip']['callbacks']['label'] = 'function(context) {
    var value = context.raw;
    return value == "1" ? "active" : "inactive";
}';

// Callback for Y scale (JS function)
$options['scales']['y']['ticks']['callback'] = 'function(value) {
    return value == "1" ? "active" : "inactive";
}';

$datasets[0]['backgroundColor'] = 'rgba(243, 47, 99, 0.20)';
$datasets[0]['borderColor'] = '#F32F63';
$datasets[0]['label'] = 'Motion detection activity';

unset($motionServiceController, $motionDailyStatus, $motionStatus);
