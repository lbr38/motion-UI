<div id="motion-stats-div">
    <div id="motion-stats-labels-data">
        <?php
        $mymotion = new \Controllers\Motion();

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
            $eventChartLabels .= "$dateLoop, ";

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

        /**
         *  Following spans will store labels and data for the charts to load
         *  The spans will get reloaded with new labels and data everytime new dates are selected
         */ ?>

        <span id="motion-event-chart-labels-data" labels="<?= $eventChartLabels ?>" event-data="<?= $eventChartData ?>" files-data="<?= $filesChartData ?>"></span>
        <span id="motion-status-chart-labels-data" labels="<?= $statusChartLabels ?>" status-data="<?= $statusChartData ?>"></span>
    </div>

    <div>
        <form id="statsDateForm" autocomplete="off">
            <input type="date" name="dateStart" class="input-small" value="<?= $statsDateStart ?>" />
            <input type="date" name="dateEnd" class="input-small" value="<?= $statsDateEnd ?>" />

            <button type="submit" class="btn-small-green">Show</button>
        </form>
    </div>

    <div id="motion-stats-container" class="config-div">
        <div>
            <canvas id="motion-event-chart"></canvas>
        </div>

        <div>
            <canvas id="motion-status-chart"></canvas>
        </div>
    </div>
</div>