<?php
$motionServiceController = new \Controllers\Motion\Service();
$labels = [];
$datasets = [];
$options = [];

/**
 *  The motion start and stop chart will print data on 24 hours
 */
$motionDailyStatus = $motionServiceController->getMotionServiceStatusStats($timeStart, $timeEnd);

foreach ($motionDailyStatus as $motionStatus) {
    $labels[] = $motionStatus['Timestamp'] * 1000;

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
$labels[] = time() * 1000;

if ($motionServiceController->isRunning()) {
    $datasets[0]['data'][] = '1';
} else {
    $datasets[0]['data'][] = '0';
}

/**
 *  Prepare chart data
 */
$options['title']['text'] = 'Motion detection activity';
$options['yaxis']['min'] = 0;
$options['yaxis']['max'] = 1;
$options['yaxis']['tickAmount'] = 1;
$options['yaxis']['labels']['formatterName'] = 'activeState';
$datasets[0]['color'] = '#F32F63';
$datasets[0]['name'] = 'Motion detection activity';

unset($motionServiceController, $motionDailyStatus, $motionStatus);
