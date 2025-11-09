<?php ob_start(); ?>

<div class="flex flex-direction-column row-gap-20">
    <div>
        <h5 class="margin-top-0">LIVE STREAM</h5>

        <h6>GO2RTC LOGS</h6>
        <p class="note">View go2rtc streaming server logs.</p>

        <div class="flex align-item-center column-gap-10">
            <?php
            $logFiles = glob(LOGS_DIR . '/go2rtc/*.log');

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
            $logFiles = glob(LOGS_DIR . '/motion/*.log');

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
    </div>

    <div>
        <hr>
    </div>

    <div>
        <h5 class="margin-top-0">SERVICE UNITS</h5>

        <div class="flex flex-direction-column row-gap-20">
            <?php
            foreach ($units as $name => $properties) : ?>
                <div>
                    <div class="flex align-item-center column-gap-5">
                        <h6 class="margin-top-0"><?= strtoupper($properties['title']) ?></h6>
                        <img src="/assets/icons/info.svg" class="icon-lowopacity icon-small icon-np unit-tooltip" unit="<?= $name ?>" description="<?= $properties['description'] ?>" />
                    </div>

                    <div class="flex align-item-center column-gap-5">
                        <?php
                        if (\Controllers\Service\Service::isRunning($name)) {
                            echo '<img src="/assets/icons/check.svg" class="icon-np" />';
                            echo '<p title="This service unit is currently running">Running</p>';
                        } else {
                            echo '<p class="note" title="This service unit is currently not running">Not running</p>';
                        } ?>
                    </div>

                    <?php
                    // Get logs for this unit
                    $logDir = $properties['log-dir'] ?? $name;
                    $logs   = glob(SERVICE_LOGS_DIR . '/' . $logDir . '/*.log');
                    rsort($logs);

                    if (!empty($logs)) { ?>
                        <div class="flex align-item-center column-gap-10 unit-logs-container">
                            <select unit="<?= $name ?>">
                                <?php
                                foreach ($logs as $log) {
                                    $logFile = basename($log);
                                    echo '<option value="' . $logFile . '">' . $logFile . '</option>';
                                } ?>
                            </select>
                            <p><span class="unit-log-view-btn btn-xsmall-green" unit="<?= $name ?>" title="View log">View</span></p>
                        </div>
                        <?php
                    } ?>
                </div>
                <?php
            endforeach;

            unset($units, $name, $properties); ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$slidePanelName = 'general/logs';
$slidePanelTitle = 'LOGS';

include(ROOT . '/views/includes/slide-panel.inc.php');
