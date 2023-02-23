<section id="main-buttons-section" class="main-buttons-container">
    <?php
    if ($cameraTotal > 0) :
        if ($settings['Stream_on_live_page'] == 'true') : ?>
            <div class="item">
                <p class="center"><b>Live</b></p>
                <a href="/live">
                    <button class="btn-square-green"><img src="resources/icons/camera.svg" class="icon" /></button>
                </a>
                <span class="block center lowopacity">Visualize</span>
            </div>
            <?php
        endif;

        if ($settings['Motion_start_btn'] == 'true') : ?>
            <div id="motion-start-div" class="item">
                <p class="center"><b>Motion</b></p>
                <?php
                if ($motionActive === false) : ?>
                    <button id="start-motion-btn" class="btn-square-green" title="Start motion service now">
                        <img src="resources/icons/power.svg" class="icon" />
                    </button>
                    <span class="block center lowopacity">Start capture</span>
                    
                    <?php
                endif;
                if ($motionActive === true) {
                    echo '<button id="stop-motion-btn" class="btn-square-red" title="Stop motion service"><img src="resources/icons/power.svg" class="icon" /></button>';
                    echo '<span class="block center lowopacity">Stop capture</span>';
                } ?>
            </div>
            <?php
        endif;

        /**
         *  Include autostart btn and settings
         */
        if ($settings['Motion_autostart_btn'] == 'true') : ?>
            <div id="motion-autostart-container" class="item">
                <p class="center"><b>Motion: autostart</b></p>

                <?php
                if ($motionAutostartEnabled == "disabled") : ?>
                    <button id="enable-autostart-btn" class="btn-square-green" title="Enable motion service autostart">
                        <img src="resources/icons/time.svg" class="icon" />
                    </button>
                    <span class="block center lowopacity">Enable and configure autostart</span>
                    <?php
                endif;
                if ($motionAutostartEnabled == "enabled") : ?>
                    <button id="disable-autostart-btn" class="btn-square-red" title="Disable motion service autostart">
                        <img src="resources/icons/time.svg" class="icon" />
                    </button>
                    <span class="block center lowopacity">Disable autostart</span>
                    <?php
                endif ?>

                <br>

                <div id="autostart-btn-div">
                    <?php
                    if ($motionAutostartEnabled == "enabled") : ?>
                        <div id="configure-autostart-btn" class="slide-btn" title="Configure autostart">
                            <img src="resources/icons/cog.svg" />
                            <span>Configure autostart</span>
                        </div>
                        <?php
                    endif ?>
                </div>
            </div>
            <?php
        endif;

        /**
         *  Include alert btn and settings
         */
        if ($settings['Motion_alert_btn'] == 'true') : ?>
            <div id="motion-alert-container" class="item">
                <p class="center"><b>Motion: alerts</b></p>

                <?php
                if ($alertEnabled == "disabled") {
                    echo '<button type="button" id="enable-alert-btn" class="btn-square-green" title="Enable motion alerts"><img src="resources/icons/alarm.svg" class="icon"></button>';
                    echo '<span class="block center lowopacity">Enable and configure alerts</span>';
                }
                if ($alertEnabled == "enabled") {
                    echo '<button type="button" id="disable-alert-btn" class="btn-square-red" title="Disable motion alerts"><img src="resources/icons/alarm.svg" class="icon"></button>';
                    echo '<span class="block center lowopacity">Disable alerts</span>';
                } ?>

                <br>

                <div id="alert-btn-div">
                    <?php
                    if ($alertEnabled == "enabled") : ?>
                        <div id="configure-alerts-btn" class="slide-btn" title="Configure alerts">
                            <img src="resources/icons/cog.svg" />
                            <span>Configure alerts</span>
                        </div>
                        <?php
                    endif ?>
                </div>
            </div>
            <?php
        endif;
    endif ?>
</section>