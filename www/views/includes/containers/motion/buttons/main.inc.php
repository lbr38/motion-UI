<section class="main-buttons-container reloadable-container" container="motion/buttons/main">
    <div class="main-buttons">

        <div class="item">
            <div class="flex flex-direction-column justify-center row-gap-15">
                <h6 class="center">MOTION</h6>
                <?php
                if (!$motionActive) {
                    $status = 'start';
                    $title = 'Start motion service now';
                    $color = 'green';
                }
                if ($motionActive) {
                    $status = 'stop';
                    $title = 'Stop motion service';
                    $color = 'red';
                } ?>

                <button class="btn-round-<?= $color ?> start-stop-service-btn relative" status="<?= $status ?>" title="<?= $title ?>">
                    <img src="/assets/icons/power.svg" class="icon" />
                </button>

                <span class="block center lowopacity-cst"><?= ucfirst($status) ?> motion detection</span>
            </div>

            <!-- <div class="flex flex-direction-column row-gap-10"> -->
                <?php
                // if ($motionActive) {
                //     echo '<img src="/assets/icons/update.svg" class="icon" title="Restart motion" />';
                // } ?>
                <!-- <img id="view-motion-log-btn" src="/assets/icons/file.svg" class="icon" title="View motion log" /> -->
            <!-- </div> -->
        </div>

        <div class="item">
            <div class="flex flex-direction-column justify-center row-gap-15">
                <h6 class="center">MOTION AUTOSTART</h6>
                <?php
                if ($motionAutostartEnabled == "disabled") : ?>
                    <div class="flex justify-center">
                        <button id="enable-autostart-btn" class="btn-round-green" title="Enable motion service autostart">
                            <img src="/assets/icons/time.svg" class="icon" />
                        </button>
                    </div>

                    <span class="block center lowopacity-cst">Enable and configure autostart</span>
                    <?php
                endif;
                if ($motionAutostartEnabled == "enabled") : ?>
                    <div class="flex justify-center">
                        <button type="button" id="disable-autostart-btn" class="btn-semi-circle-red-left" title="Disable motion service autostart">
                            <img src="/assets/icons/time.svg" class="icon">
                        </button>

                        <button type="button" class="btn-semi-circle-blue-right get-panel-btn" panel="motion/autostart" title="Configure autostart">
                            <img src="/assets/icons/cog.svg" class="icon">
                        </button>
                    </div>

                    <p class="block center lowopacity-cst">Disable or configure autostart</p>
                    <?php
                endif ?>
            </div>
        </div>

        <div class="item">
            <div class="flex flex-direction-column justify-center row-gap-15">
                <h6 class="center">MOTION ALERTS</h6>
                <?php
                if (ALERT_ENABLED == "disabled") : ?>
                    <div class="flex justify-center">
                        <button type="button" id="enable-alert-btn" class="btn-round-green" title="Enable motion alerts">
                            <img src="/assets/icons/alarm.svg" class="icon">
                        </button>
                    </div>
        
                    <p class="block center lowopacity-cst">Enable and configure alerts</p>
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
                    
                    <p class="block center lowopacity-cst">Disable or configure alerts</p>
                    <?php
                endif ?>
            </div>
        </div>
    </div>
</section>