<section class="main-buttons-container reloadable-container" container="motion/buttons/main">
    <?php
    if ($cameraTotal > 0) : ?>
        <div id="motion-start-div" class="item">
            <p class="center"><b>Motion</b></p>
            <?php
            if ($motionActive === false) : ?>
                <button id="start-motion-btn" class="btn-square-green" title="Start motion service now">
                    <img src="/assets/icons/power.svg" class="icon" />
                </button>
                <span class="block center lowopacity-cst">Start capture</span>
                <?php
            endif;
            if ($motionActive === true) : ?>
                <button id="stop-motion-btn" class="btn-square-red" title="Stop motion service"><img src="/assets/icons/power.svg" class="icon" /></button>
                <span class="block center lowopacity-cst">Stop capture</span>
                <?php
            endif ?>
        </div>
        <?php

        /**
         *  Include autostart btn and settings
         */ ?>
        <div class="item">
            <p class="center"><b>Motion: autostart</b></p>

            <?php
            if ($motionAutostartEnabled == "disabled") : ?>
                <button id="enable-autostart-btn" class="btn-square-green" title="Enable motion service autostart">
                    <img src="/assets/icons/time.svg" class="icon" />
                </button>
                <span class="block center lowopacity-cst">Enable and configure autostart</span>
                <?php
            endif;
            if ($motionAutostartEnabled == "enabled") : ?>
                <button id="disable-autostart-btn" class="btn-square-red" title="Disable motion service autostart">
                    <img src="/assets/icons/time.svg" class="icon" />
                </button>
                <span class="block center lowopacity-cst">Disable autostart</span>
                <?php
            endif ?>

            <br>

            <div id="autostart-btn-div">
                <?php
                if ($motionAutostartEnabled == "enabled") : ?>
                    <div class="slide-btn slide-panel-btn" slide-panel="autostart" title="Configure autostart">
                        <img src="/assets/icons/cog.svg" />
                        <span>Configure autostart</span>
                    </div>
                    <?php
                endif ?>
            </div>
        </div>
        <?php

        /**
         *  Include alert btn and settings
         */ ?>
        <div class="item">
            <p class="center"><b>Motion: alerts</b></p>

            <?php
            if (ALERT_ENABLED == "disabled") : ?>
                <button type="button" id="enable-alert-btn" class="btn-square-green" title="Enable motion alerts"><img src="/assets/icons/alarm.svg" class="icon"></button>
                <span class="block center lowopacity-cst">Enable and configure alerts</span>
                <?php
            endif;
            if (ALERT_ENABLED == "enabled") : ?>
                <button type="button" id="disable-alert-btn" class="btn-square-red" title="Disable motion alerts"><img src="/assets/icons/alarm.svg" class="icon"></button>
                <span class="block center lowopacity-cst">Disable alerts</span>
                <?php
            endif ?>

            <br>

            <div id="alert-btn-div">
                <?php
                if (ALERT_ENABLED == "enabled") : ?>
                    <div class="slide-btn slide-panel-btn" slide-panel="alert" title="Configure alerts">
                        <img src="/assets/icons/cog.svg" />
                        <span>Configure alerts</span>
                    </div>
                    <?php
                endif ?>
            </div>
        </div>
        <?php
    endif ?>
</section>