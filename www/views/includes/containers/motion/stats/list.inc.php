<section class="main-container reloadable-container" container="motion/stats/list">
    <div id="motion-stats-div">
        <div id="motion-stats-labels-data">
            <?php
            $eventChartLabels = '';
            $eventChartData = '';
            $filesChartData = '';
            $statusChartLabels = '';
            $statusChartData = '';

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
                $eventCount = count($mymotionEvent->getByDate($dateLoop));

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
                $filesCount = $mymotionEvent->getTotalFileByDate($dateLoop);

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
                $eventChartLabels .= "$dateLoop, ";

                /**
                 *  Increment date to process next date until reaching today's date +1
                 */
                $dateLoop = date('Y-m-d', strtotime('+1 day', strtotime($dateLoop)));
            }

            /**
             *  The motion start and stop chart will print data on 48 hours
             */
            $motionDailyStatus = $mymotionService->getMotionServiceStatusStats();

            foreach ($motionDailyStatus as $motionStatus) {
                $statusChartLabels .= $motionStatus['Time'] . ', ';

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
            $statusChartLabels .= date('H:i:s') . ', ';

            if ($mymotionService->isRunning()) {
                $statusChartData .= '1, ';
            } else {
                $statusChartData .= '0, ';
            }

            /**
             *  Remove last comma on all label and data vars
             */
            $eventChartLabels = rtrim($eventChartLabels, ', ');
            $eventChartData = rtrim($eventChartData, ', ');
            $filesChartData = rtrim($filesChartData, ', ');
            $statusChartLabels = rtrim($statusChartLabels, ', ');
            $statusChartData = rtrim($statusChartData, ', ');

            /**
             *  Following spans will store labels and data for the charts to load
             *  The spans will get reloaded with new labels and data everytime new dates are selected
             */ ?>
            <span id="motion-event-chart-labels-data" labels="<?= $eventChartLabels ?>" event-data="<?= $eventChartData ?>" files-data="<?= $filesChartData ?>"></span>
            <span id="motion-status-chart-labels-data" labels="<?= $statusChartLabels ?>" status-data="<?= $statusChartData ?>"></span>
        </div>

        <div>
            <p class="lowopacity-cst">Period:</p>
            <div class="flex column-gap-10">
                <input type="date" name="dateStart" class="input-medium stats-date-input" value="<?= $statsDateStart ?>" />
                <input type="date" name="dateEnd" class="input-medium stats-date-input" value="<?= $statsDateEnd ?>" />
            </div>
        </div>

        <div id="motion-stats-container">
            <div class="div-generic-blue">
                <canvas id="motion-event-chart"></canvas>
            </div>

            <div class="div-generic-blue">
                <canvas id="motion-status-chart"></canvas>
            </div>
        </div>
    </div>

    <script>
        /**
         *  Create empty motion status and events charts
         */
        var ctx = document.getElementById('motion-event-chart').getContext('2d');
        var myEventChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                {
                    data: [],
                    label: "Motion events",
                    borderColor: '#3e95cd',
                    fill: false
                },
                {
                    data: [],
                    label: "Motion files recorded",
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
        var yLabels = {
            0 : 'inactive',
            1 : 'active'
        }
        var ctx = document.getElementById('motion-status-chart').getContext('2d');
        var myMotionStatusChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    label: "Motion service activity (24h)",
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
                            callback: function (value, index, values) {
                                return yLabels[value];
                            }
                        }
                    }
                },
            }
        });

        $(document).ready(function() {
            /**
             *  Inject charts labels and data
             */
            loadAllStatsCharts();
        });
    </script>
</section>