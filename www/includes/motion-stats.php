<?php
$mymotion = new \Controllers\Motion();

$eventChartLabels = '';
$eventChartData = '';
$filesChartData = '';

$statusChartLabels = '';
$statusChartData = '';

/**
 *  The event chart will print data from 1 week ago to today
 *  Initialize $dateLoop to 1 week ago
 */
$dateLoop = date('Y-m-d', strtotime('-1 week', strtotime(DATE_YMD)));

/**
 *  Process each dates until today's date (that is also processed)
 */
while ($dateLoop != date('Y-m-d', strtotime('+1 day', strtotime(DATE_YMD)))) {
    /**
     *  Get total event for the actual day
     */
    $eventCount = $mymotion->getDailyEventCount($dateLoop);

    /**
     *  Add count to data
     */
    if (!empty($eventCount)) {
        $eventChartData .= $eventCount . ', ';
    } else {
        $eventChartData .= '0, ';
    }

    /**
     *  Get total files for the actual day
     */
    $filesCount = $mymotion->getDailyFileCount($dateLoop);

    /**
     *  Add count to data
     */
    if (!empty($filesCount)) {
        $filesChartData .= $filesCount . ', ';
    } else {
        $filesChartData .= '0, ';
    }

    /**
     *  Add actual day to the labels
     */
    $eventChartLabels .= "'$dateLoop', ";

    /**
     *  Increment date to process next date until reaching today's date +1
     */
    $dateLoop = date('Y-m-d', strtotime('+1 day', strtotime($dateLoop)));
}

/**
 *  The motion start and stop chart will print data on 48 hours
 */
$motionDailyStatus = $mymotion->getMotionServiceStatus();

foreach ($motionDailyStatus as $motionStatus) {
    $statusChartLabels .= '"' . $motionStatus['Time'] . '", ';

    if ($motionStatus['Status'] == 'inactive') {
        $statusChartData .= '0, ';
    }
    if ($motionStatus['Status'] == 'active') {
        $statusChartData .= '1, ';
    }
}
/**
 *  Then add current motion status to the list
 */
$statusChartLabels .= '"' . date('H:i:s') . '", ';

if ($mymotion->getStatus() == 'active') {
    $statusChartData .= '1, ';
} else {
    $statusChartData .= '0, ';
}

/**
 *  Remove last comma on all label and data vars
 */
$eventChartLabels = rtrim($eventChartLabels, ', ');
$eventChartData  = rtrim($eventChartData, ', ');
$filesChartData = rtrim($filesChartData, ', ');
$statusChartLabels = rtrim($statusChartLabels, ', ');
$statusChartData = rtrim($statusChartData, ', ');
?>

<div id="motion-stats-div">

    <h2>Motion: stats</h2>

    <div id="motion-stats-container" class="config-div">
        <div>
            <canvas id="motion-event-chart"></canvas>
            <script>
                var ctx = document.getElementById('motion-event-chart').getContext('2d');
                var myRepoAccessChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [<?= $eventChartLabels ?>],
                        datasets: [
                            {
                            data: [<?= $eventChartData ?>],
                            label: "Total events per day",
                            borderColor: '#3e95cd',
                            fill: false
                            },
                            {
                                data: [<?= $filesChartData ?>],
                                label: "Total files recorded per day",
                                borderColor: '#ea974d',
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        tension: 0.2,
                        scales: {
                            x: {
                                display: true,
                            },
                            y: {
                                beginAtZero: true,
                                display: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                    }
                });
            </script>
        </div>

        <div>
            <canvas id="motion-status-chart"></canvas>
            <script>
                var yLabels = {
                    0 : 'inactive',
                    1 : 'active'
                }
                var ctx = document.getElementById('motion-status-chart').getContext('2d');
                var myRepoAccessChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [<?= $statusChartLabels ?>],
                        datasets: [{
                            data: [<?= $statusChartData ?>],
                            label: "Motion service activity",
                            borderColor: '#d8524e',
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        tension: 0.2,
                        scales: {
                            x: {
                                display: true,
                            },
                            y: {
                                beginAtZero: true,
                                display: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value, index, values) {
                                        return yLabels[value];
                                    }
                                }
                            }
                        },
                    }
                });
            </script>
        </div>
    </div>
</div>