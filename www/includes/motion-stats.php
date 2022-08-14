<?php
$mymotion = new \Controllers\Motion();

$chartLabels = '';
$eventChartData = '';
$filesChartData = '';

/**
 *
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
    $chartLabels .= "'$dateLoop', ";

    /**
     *  Increment date to process next date until reaching today's date +1
     */
    $dateLoop = date('Y-m-d', strtotime('+1 day', strtotime($dateLoop)));
}

/**
 *  Remove last comma
 */
$chartLabels = rtrim($chartLabels, ', ');
$eventChartData  = rtrim($eventChartData, ', ');
$filesChartData = rtrim($filesChartData, ', ');
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
                        labels: [<?= $chartLabels ?>],
                        datasets: [{
                            data: [<?= $eventChartData ?>],
                            label: "Total events per day",
                            borderColor: '#3e95cd',
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
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
            <canvas id="motion-files-chart"></canvas>
            <script>
                var ctx = document.getElementById('motion-files-chart').getContext('2d');
                var myRepoAccessChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [<?= $chartLabels ?>],
                        datasets: [{
                            data: [<?= $filesChartData ?>],
                            label: "Total files recorded per day",
                            borderColor: '#ea974d',
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
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
    </div>
</div>