<section class="main-buttons-container reloadable-container" container="motion/buttons/main">
    <div class="main-buttons">
        <div class="item">
            <div class="flex flex-direction-column justify-center row-gap-15">
                <h6 class="center"><?= __('MOTION DETECTION') ?></h6>
                <?php
                if (!$motionActive) {
                    $status = 'start';
                    $text = __('Start');
                    $title = __('Start motion detection');
                    $color = 'green';
                }
                if ($motionActive) {
                    $status = 'stop';
                    $text = __('Stop');
                    $title = __('Stop motion detection');
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
                <h6 class="center"><?= __('MOTION DETECTION AUTOSTART') ?></h6>
                <?php
                if ($motionAutostartEnabled == "disabled") : ?>
                    <div class="flex justify-center">
                        <button id="enable-autostart-btn" class="btn-round-green" title="<?= __('Enable motion detection autostart') ?>">
                            <img src="/assets/icons/time.svg" class="icon" />
                        </button>
                    </div>

                    <p class="block center mediumopacity-cst"><?= __('Enable & configure') ?></p>
                    <?php
                endif;

                if ($motionAutostartEnabled == "enabled") : ?>
                    <div class="flex justify-center">
                        <button type="button" id="disable-autostart-btn" class="btn-semi-circle-red-left" title="<?= __('Disable motion detection autostart') ?>">
                            <img src="/assets/icons/time.svg" class="icon">
                        </button>

                        <button type="button" class="btn-semi-circle-blue-right get-panel-btn" panel="motion/autostart" title="<?= __('Configure autostart') ?>">
                            <img src="/assets/icons/cog.svg" class="icon">
                        </button>
                    </div>

                    <p class="block center mediumopacity-cst"><?= __('Disable or configure') ?></p>
                    <?php
                endif ?>
            </div>
        </div>

        <div class="item">
            <div class="flex flex-direction-column justify-center row-gap-15">
                <h6 class="center"><?= __('MOTION DETECTION ALERTS') ?></h6>
                <?php
                if (ALERT_ENABLED == "disabled") : ?>
                    <div class="flex justify-center">
                        <button type="button" id="enable-alert-btn" class="btn-round-green" title="<?= __('Enable motion alerts') ?>">
                            <img src="/assets/icons/alarm.svg" class="icon">
                        </button>
                    </div>
        
                    <p class="block center mediumopacity-cst"><?= __('Enable & configure') ?></p>
                    <?php
                endif;

                if (ALERT_ENABLED == "enabled") : ?>
                    <div class="flex justify-center">
                        <button type="button" id="disable-alert-btn" class="btn-semi-circle-red-left" title="<?= __('Disable motion alerts') ?>">
                            <img src="/assets/icons/alarm.svg" class="icon">
                        </button>

                        <button type="button" class="btn-semi-circle-blue-right get-panel-btn" panel="motion/alert" title="<?= __('Configure alerts') ?>">
                            <img src="/assets/icons/cog.svg" class="icon">
                        </button>
                    </div>
                    
                    <p class="block center mediumopacity-cst"><?= __('Disable or configure') ?></p>
                    <?php
                endif ?>
            </div>
        </div>
    </div>
</section>