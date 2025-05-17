<?php ob_start(); ?>

<div class="flex flex-direction-column row-gap-20">
    <div>
        <h5 class="margin-top-0">LIVE STREAM</h5>

        <h6>GO2RTC LOGS</h6>
        <p class="note">View go2rtc streaming server logs.</p>

        <div class="flex align-item-center column-gap-10">
            <?php
            $logFiles = glob(GO2RTC_DIR . '/logs/*.log');

            if (empty($logFiles)) {
                echo '<p class="note">No logs for now.</p>';
            }

            if (!empty($logFiles)) :
                rsort($logFiles); ?>

                <select id="go2rtc-log-select">
                    <?php
                    foreach ($logFiles as $logFile) {
                        if (basename($logFile) == 'go2rtc.log') {
                            continue;
                        }

                        echo '<option value="' . basename($logFile) . '">' . basename($logFile) . '</option>';
                    } ?>
                </select>

                <div>
                    <button id="go2rtc-log-btn" type="button" class="btn-xsmall-green">View</button>
                </div>
                <?php
            endif ?>
        </div>
    </div>

    <div>
        <hr>
    </div>

    <div>
        <h5 class="margin-top-0">MOTION DETECTION</h5>

        <h6>SERVICE LOGS</h6>
        <p class="note">View motion detection service logs.</p>

        <div class="flex align-item-center column-gap-10">
            <?php
            $logFiles = glob('/var/log/motion/*.log');

            if (empty($logFiles)) {
                echo '<p class="note">No logs for now.</p>';
            }

            if (!empty($logFiles)) :
                rsort($logFiles); ?>

                <select id="motion-log-select">
                    <?php
                    foreach ($logFiles as $logFile) {
                        if (basename($logFile) == 'motion.log') {
                            continue;
                        }

                        echo '<option value="' . basename($logFile) . '">' . basename($logFile) . '</option>';
                    } ?>
                </select>

                <div>
                    <button id="motion-log-btn" type="button" class="btn-xsmall-green">View</button>
                </div>
                <?php
            endif ?>
        </div>

        <h6>AUTOSTART LOGS</h6>
        <p class="note">View motion detection autostart logs.</p>

        <div class="flex align-item-center column-gap-10">
            <?php
            $logFiles = glob(AUTOSTART_LOGS_DIR . '/*.log');

            if (empty($logFiles)) {
                echo '<p class="note">No logs for now.</p>';
            }

            if (!empty($logFiles)) :
                rsort($logFiles); ?>

                <select id="motion-autostart-log-select">
                    <?php
                    foreach ($logFiles as $logFile) {
                        echo '<option value="' . basename($logFile) . '">' . basename($logFile) . '</option>';
                    } ?>
                </select>

                <div>
                    <button id="motion-autostart-log-btn" type="button" class="btn-xsmall-green">View</button>
                </div>
                <?php
            endif ?>
        </div>        
    </div>
</div>

<?php
$content = ob_get_clean();
$slidePanelName = 'general/logs';
$slidePanelTitle = 'LOGS';

include(ROOT . '/views/includes/slide-panel.inc.php');
