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
            <h6>SELECT PERIOD</h6>
            <div class="flex column-gap-10">
                <input type="date" name="dateStart" class="input-medium stats-date-input" value="<?= $statsDateStart ?>" />
                <input type="date" name="dateEnd" class="input-medium stats-date-input" value="<?= $statsDateEnd ?>" />
            </div>
        </div>

        <div id="motion-stats-container" class="margin-top-30">
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
                        label: "Events",
                        borderColor: '#15bf7f',
                        backgroundColor: 'rgba(21,191,127,0.20)',
                        fill: true
                    },
                    {
                        data: [],
                        label: "Media files recorded",
                        borderColor: '#ffb536',
                        backgroundColor: 'rgba(255, 181, 54, 0.20)',
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tension: 0.2,
                plugins: {
                    title: {
                        display: true,
                        position: 'top',
                        text: "Motion detection",
                        color: '#8A99AA',
                        font: {
                            size: 14,
                            family: 'Roboto',
                        },
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 14,
                                family: 'Roboto',
                            },
                            color: '#8A99AA',
                            useBorderRadius: true,
                            borderRadius: 5,
                        },
                    }
                },
                scales: {
                    x: {
                        display: true,
                        ticks: {
                            color: '#8A99AA',
                            font: {
                                size: 13,
                                family: 'Roboto'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        display: true,
                        ticks: {
                            color: '#8A99AA',
                            font: {
                                size: 13,
                                family: 'Roboto'
                            },
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
                    label: "Motion detection activity",
                    borderColor: '#F32F63',
                    backgroundColor: 'rgba(243, 47, 99, 0.20)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        position: 'top',
                        text: "Motion detection activity (24h)",
                        color: '#8A99AA',
                        font: {
                            size: 14,
                            family: 'Roboto',
                        },
                    },
                    legend: {
                        display: false
                    }
                },
                tension: 0.2,
                scales: {
                    x: {
                        display: true,
                        ticks: {
                            color: '#8A99AA',
                            font: {
                                size: 13,
                                family: 'Roboto'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        display: true,
                        ticks: {
                            color: '#8A99AA',
                            font: {
                                size: 13,
                                family: 'Roboto'
                            },
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