<section class="main-buttons-container reloadable-container" container="motion/buttons/main">
    <div class="main-buttons">

        <div class="item">
            <div class="flex flex-direction-column justify-center row-gap-15">
                <h6 class="center">MOTION</h6>
                <?php
                if ($motionActive === false) : ?>
                    <button class="btn-square-green start-stop-service-btn" status="start" title="Start motion service now">
                        <img src="/assets/icons/power.svg" class="icon" />
                    </button>
                    <span class="block center lowopacity-cst">Start capture</span>
                    <?php
                endif;

                if ($motionActive === true) : ?>
                    <button class="btn-square-red start-stop-service-btn" status="stop" title="Stop motion service">
                        <img src="/assets/icons/power.svg" class="icon" />
                    </button>
                    <span class="block center lowopacity-cst">Stop capture</span>
                    <?php
                endif ?>
            </div>
        </div>

        <div class="item">
            <div class="flex flex-direction-column justify-center row-gap-15">
                <h6 class="center">MOTION AUTOSTART</h6>
                <?php
                if ($motionAutostartEnabled == "disabled") : ?>
                    <div class="flex justify-center">
                        <button id="enable-autostart-btn" class="btn-square-green" title="Enable motion service autostart">
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

                        <button type="button" class="btn-semi-circle-blue-right slide-panel-btn" slide-panel="autostart" title="Configure autostart">
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
                        <button type="button" id="enable-alert-btn" class="btn-square-green" title="Enable motion alerts">
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

                        <button type="button" class="btn-semi-circle-blue-right slide-panel-btn" slide-panel="alert" title="Configure alerts">
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