<section class="main-buttons-container reloadable-container" container="motion/buttons/main">
    <div class="main-buttons">
        <div class="item">
            <div class="flex flex-direction-column justify-center row-gap-15">
                <h6 class="center"><?= LC['views']['containers']['motion/buttons/main']['h6']['motion_detection'] ?></h6>
                <?php
                if (!$motionActive) {
                    $status = 'start';
                    $text = LC['views']['containers']['motion/buttons/main']['p']['motion_detection_start'];
                    $title = LC['views']['containers']['motion/buttons/main']['btn']['motion_detection_start_title'];
                    $color = 'green';
                }
                if ($motionActive) {
                    $status = 'stop';
                    $text = LC['views']['containers']['motion/buttons/main']['p']['motion_detection_stop'];
                    $title = LC['views']['containers']['motion/buttons/main']['btn']['motion_detection_stop_title'];
                    $color = 'red';
                } ?>

                <button class="btn-round-<?= $color ?> start-stop-service-btn relative" status="<?= $status ?>" title="<?= $title ?>">
                    <img src="/assets/icons/power.svg" class="icon" />
                </button>

                <p class="block center mediumopacity-cst"><?= $text ?></p>
            </div>

            <div class="flex flex-direction-column row-gap-10">
                <?php
                // if ($motionActive) {
                //     echo '<img src="/assets/icons/update.svg" class="icon" title="Restart motion" />';
                // } ?>
            </div>
        </div>

        <div class="item">
            <div class="flex flex-direction-column justify-center row-gap-15">
                <h6 class="center"><?= LC['views']['containers']['motion/buttons/main']['h6']['motion_detection_autostart'] ?></h6>
                <?php
                if ($motionAutostartEnabled == "disabled") : ?>
                    <div class="flex justify-center">
                        <button id="enable-autostart-btn" class="btn-round-green" title="Enable motion detection autostart">
                            <img src="/assets/icons/time.svg" class="icon" />
                        </button>
                    </div>

                    <p class="block center mediumopacity-cst"><?= LC['views']['containers']['motion/buttons/main']['p']['motion_detection_autostart_enable'] ?></p>
                    <?php
                endif;

                if ($motionAutostartEnabled == "enabled") : ?>
                    <div class="flex justify-center">
                        <button type="button" id="disable-autostart-btn" class="btn-semi-circle-red-left" title="Disable motion detection autostart">
                            <img src="/assets/icons/time.svg" class="icon">
                        </button>

                        <button type="button" class="btn-semi-circle-blue-right get-panel-btn" panel="motion/autostart" title="Configure autostart">
                            <img src="/assets/icons/cog.svg" class="icon">
                        </button>
                    </div>

                    <p class="block center mediumopacity-cst"><?= LC['views']['containers']['motion/buttons/main']['p']['motion_detection_autostart_disable'] ?></p>
                    <?php
                endif ?>
            </div>
        </div>

        <div class="item">
            <div class="flex flex-direction-column justify-center row-gap-15">
                <h6 class="center"><?= LC['views']['containers']['motion/buttons/main']['h6']['motion_detection_alerts'] ?></h6>
                <?php
                if (ALERT_ENABLED == "disabled") : ?>
                    <div class="flex justify-center">
                        <button type="button" id="enable-alert-btn" class="btn-round-green" title="Enable motion alerts">
                            <img src="/assets/icons/alarm.svg" class="icon">
                        </button>
                    </div>
        
                    <p class="block center mediumopacity-cst"><?= LC['views']['containers']['motion/buttons/main']['p']['motion_detection_alerts_enable'] ?></p>
                    <?php
                endif;

                if (ALERT_ENABLED == "enabled") : ?>
                    <div class="flex justify-center">
                        <button type="button" id="disable-alert-btn" class="btn-semi-circle-red-left" title="Disable motion alerts">
                            <img src="/assets/icons/alarm.svg" class="icon">
                        </button>

                        <button type="button" class="btn-semi-circle-blue-right get-panel-btn" panel="motion/alert" title="Configure alerts">
                            <img src="/assets/icons/cog.svg" class="icon">
                        </button>
                    </div>
                    
                    <p class="block center mediumopacity-cst"><?= LC['views']['containers']['motion/buttons/main']['p']['motion_detection_alerts_disable'] ?></p>
                    <?php
                endif ?>
            </div>
        </div>
    </div>
</section>