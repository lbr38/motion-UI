<?php
$motionEventController = new \Controllers\Motion\Event();
$datasets = [];
$labels = [];
$options = [];

/**
 *  Get date start and end from cookies if there are, else set a default interval of 7 days.
 */
if (empty($_COOKIE['statsDateStart'])) {
    $statsDateStart = date('Y-m-d', strtotime('-7 day', strtotime(DATE_YMD)));
} else {
    $statsDateStart = $_COOKIE['statsDateStart'];
}

if (empty($_COOKIE['statsDateEnd'])) {
    $statsDateEnd = DATE_YMD;
} else {
    $statsDateEnd = $_COOKIE['statsDateEnd'];
}

/**
 *  The event chart will print data from $statsDateStart;
 *  Initialize $dateLoop to 1 week ago
 */
$dateLoop = $statsDateStart;

/**
 *  Process each dates until $statsDateEnd is reached (this date is also processed)
 */
while ($dateLoop != date('Y-m-d', strtotime('+1 day', strtotime($statsDateEnd)))) {
    /**
     *  Get total event for the actual day
     */
    $eventCount = count($motionEventController->getByDate($dateLoop));

    /**
     *  Add count to data
     */
    $datasets[0]['data'][] = $eventCount;

    /**
     *  Get total files for the actual day
     */
    $filesCount = $motionEventController->getTotalFileByDate($dateLoop);

    /**
     *  Add count to data
     */
    $datasets[1]['data'][] = $filesCount;

    /**
     *  Add actual day to the labels
     */
    $labels[] = $dateLoop;

    /**
     *  Increment date to process next date until reaching today's date +1
     */
    $dateLoop = date('Y-m-d', strtotime('+1 day', strtotime($dateLoop)));
}

/**
 *  Prepare chart data
 */
$options['title']['text'] = 'Motion detection';
$options['legend']['display']['position'] = 'bottom';
$datasets[0]['backgroundColor'] = 'rgba(21,191,127,0.20)';
$datasets[0]['borderColor'] = '#15bf7f';
$datasets[0]['label'] = 'Events';

$datasets[1]['backgroundColor'] = 'rgba(255, 181, 54, 0.20)';
$datasets[1]['borderColor'] = '#ffb536';
$datasets[1]['label'] = 'Media files recorded';

unset($motionEventController, $dateLoop, $eventCount, $filesCount);
