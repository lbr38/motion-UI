<section class="main-buttons-container reloadable-container" container="motion/buttons/main">
    <div class="main-buttons">
        <?php
        /**
         *  Motion detection (start / stop)
         */
        if ($motionActive) {
            $status = 'stop';
            $title  = __('Stop motion detection');
        } else {
            $status = 'start';
            $title  = __('Start motion detection');
        } ?>

        <div class="motion-toggle-item">
            <button type="button" class="motion-toggle <?= $motionActive ? 'motion-toggle-on' : '' ?> start-stop-service-btn" status="<?= $status ?>" title="<?= $title ?>" aria-pressed="<?= $motionActive ? 'true' : 'false' ?>">
                <img src="/assets/icons/power.svg" class="icon" alt="" />
                <span class="motion-toggle-label"><?= __('Motion detection') ?></span>
                <span class="motion-toggle-state"><?= $motionActive ? 'ON' : 'OFF' ?></span>
            </button>
        </div>

        <?php
        /**
         *  Motion detection autostart
         */
        $autostartEnabled = ($motionAutostartEnabled == 'enabled'); ?>

        <div class="motion-toggle-item">
            <button type="button" id="<?= $autostartEnabled ? 'disable-autostart-btn' : 'enable-autostart-btn' ?>" class="motion-toggle <?= $autostartEnabled ? 'motion-toggle-on' : '' ?>" title="<?= $autostartEnabled ? __('Disable motion detection autostart') : __('Enable motion detection autostart') ?>" aria-pressed="<?= $autostartEnabled ? 'true' : 'false' ?>">
                <img src="/assets/icons/time.svg" class="icon" alt="" />
                <span class="motion-toggle-label"><?= __('Autostart') ?></span>
                <span class="motion-toggle-state"><?= $autostartEnabled ? 'ON' : 'OFF' ?></span>
            </button>

            <?php
            if ($autostartEnabled) : ?>
                <button type="button" class="motion-toggle-config get-panel-btn" panel="motion/autostart" title="<?= __('Configure autostart') ?>">
                    <img src="/assets/icons/cog.svg" class="icon" alt="" />
                </button>
                <?php
            endif ?>
        </div>

        <?php
        /**
         *  Motion detection alerts
         */
        $alertEnabled = (ALERT_ENABLED == 'enabled'); ?>

        <div class="motion-toggle-item">
            <button type="button" id="<?= $alertEnabled ? 'disable-alert-btn' : 'enable-alert-btn' ?>" class="motion-toggle <?= $alertEnabled ? 'motion-toggle-on' : '' ?>" title="<?= $alertEnabled ? __('Disable motion alerts') : __('Enable motion alerts') ?>" aria-pressed="<?= $alertEnabled ? 'true' : 'false' ?>">
                <img src="/assets/icons/alarm.svg" class="icon" alt="" />
                <span class="motion-toggle-label"><?= __('Alerts') ?></span>
                <span class="motion-toggle-state"><?= $alertEnabled ? 'ON' : 'OFF' ?></span>
            </button>

            <?php
            if ($alertEnabled) : ?>
                <button type="button" class="motion-toggle-config get-panel-btn" panel="motion/alert" title="<?= __('Configure alerts') ?>">
                    <img src="/assets/icons/cog.svg" class="icon" alt="" />
                </button>
                <?php
            endif ?>
        </div>
    </div>
</section>
