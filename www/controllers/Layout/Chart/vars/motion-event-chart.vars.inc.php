<?php
$motionEventController = new \Controllers\Motion\Event();
$datasets = [];
$labels = [];
$options = [];

/**
 *  Get date start and end from cookies if there are, else set a default interval of 7 days.
 */
$dateStart = date('Y-m-d', strtotime('-7 day', strtotime(DATE_YMD)));
$dateEnd = DATE_YMD;

/**
 *  The event chart will print data from $dateStart;
 *  Initialize $dateLoop to 1 week ago
 */
$dateLoop = $dateStart;

/**
 *  Process each dates until $dateEnd is reached (this date is also processed)
 */
while ($dateLoop != date('Y-m-d', strtotime('+1 day', strtotime($dateEnd)))) {
    // Get total event for the actual day
    $eventCount = count($motionEventController->getByDate($dateLoop));

    // Add count to data
    $datasets[0]['data'][] = $eventCount;

    // Get total files for the actual day
    $filesCount = $motionEventController->getTotalFileByDate($dateLoop);

    // Add count to data
    $datasets[1]['data'][] = $filesCount;

    // Add actual day to the labels
    $labels[] = $dateLoop;

    // Increment date to process next date until reaching today's date +1
    $dateLoop = date('Y-m-d', strtotime('+1 day', strtotime($dateLoop)));
}

/**
 *  Prepare chart data
 */
$options['title']['text'] = 'Motion detection';

$datasets[0]['color'] = '#15bf7f';
$datasets[0]['name'] = 'Events';
$datasets[1]['color'] = '#ffb536';
$datasets[1]['name'] = 'Media files recorded';

$options['toolbar']['show'] = false;

unset($motionEventController, $dateLoop, $eventCount, $filesCount);
