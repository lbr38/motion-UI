<?php
$motionEventController = new \Controllers\Motion\Event();
$datasets = [];
$labels = [];
$options = [];
$eventsCount = [];
$filesCount = [];

/**
 *  Group events and recorded files into intervals, so that the chart has a regular number of points whatever the period:
 *  hourly intervals for periods up to 3 days, daily intervals (starting at midnight, server local time) otherwise
 */
$hourly = ($timeEnd - $timeStart) <= 3 * 86400;

$intervalStart = function (int $timestamp) use ($hourly): int {
    if ($hourly) {
        return intdiv($timestamp, 3600) * 3600;
    }

    return strtotime(date('Y-m-d', $timestamp));
};

/**
 *  Initialize all intervals of the period with 0, without going beyond the current time
 */
$periodEnd = min($timeEnd, time());

for ($interval = $intervalStart($timeStart); $interval <= $periodEnd; $interval = ($hourly ? $interval + 3600 : strtotime('+1 day', $interval))) {
    $eventsCount[$interval] = 0;
    $filesCount[$interval] = 0;
}

/**
 *  Count events and recorded files for each interval
 */
foreach ($motionEventController->getBetweenDateWithFilesCount(date('Y-m-d', $timeStart), date('Y-m-d', $periodEnd)) as $event) {
    $timestamp = strtotime($event['Date_start'] . ' ' . $event['Time_start']);

    if ($timestamp === false || $timestamp < $timeStart || $timestamp > $periodEnd) {
        continue;
    }

    $interval = $intervalStart($timestamp);

    if (!isset($eventsCount[$interval])) {
        continue;
    }

    $eventsCount[$interval]++;
    $filesCount[$interval] += (int) $event['Files_count'];
}

foreach ($eventsCount as $interval => $count) {
    $labels[] = $interval * 1000;
    $datasets[0]['data'][] = $count;
    $datasets[1]['data'][] = $filesCount[$interval];
}

/**
 *  Prepare chart data
 */
$options['title']['text'] = __('Events & files recorded');
$options['legend']['show'] = false;
$datasets[0]['color'] = '#15bf7f';
$datasets[0]['name'] = __('Events');
$datasets[1]['color'] = '#ffb536';
$datasets[1]['name'] = __('Media files recorded');

unset($motionEventController, $eventsCount, $filesCount, $hourly, $intervalStart, $periodEnd, $interval, $event, $timestamp, $count);
