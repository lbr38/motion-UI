<?php
$eventChartLabels = '';
$eventChartData = '';
$dateCounter = date('Y-m-d', strtotime('-1 week', strtotime(DATE_YMD)));
$mymotion = new \Controllers\Motion();

/**
 *  On traite toutes les dates jusqu'à atteindre la date du jour (qu'on traite aussi)
 */
while ($dateCounter != date('Y-m-d', strtotime('+1 day', strtotime(DATE_YMD)))) {
    $dateEventCount = $mymotion->getDailyEventCount($dateCounter);

    if (!empty($dateEventCount)) {
        $eventChartData .= $dateEventCount . ', ';
    } else {
        $eventChartData .= '0, ';
    }

    /**
     *  Ajout de la date en cours aux labels
     */
    $eventChartLabels .= "'$dateCounter', ";

    /**
     *  On incrémente de 1 jour pour pouvori traiter la date suivante
     */
    $dateCounter = date('Y-m-d', strtotime('+1 day', strtotime($dateCounter)));
}

/**
 *  Suppression de la dernière virgule
 */
$eventChartLabels = rtrim($eventChartLabels, ', ');
$eventChartData  = rtrim($eventChartData, ', ');
?>

<div id="motion-stats-div">

    <h2>Motion: stats</h2>

    <div id="motion-stats-container">
        
        <div>
            <canvas id="motion-event-chart"></canvas>
            <script>
                var ctx = document.getElementById('motion-event-chart').getContext('2d');
                var myRepoAccessChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [<?= $eventChartLabels ?>],
                        datasets: [{
                            data: [<?= $eventChartData ?>],
                            label: "Events count",
                            borderColor: '#3e95cd',
                            fill: false
                        }]
                    },
                    options: {
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

<hr>