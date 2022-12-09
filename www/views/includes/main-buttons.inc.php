<div id="buttons-container">
    <?php
    if ($printLiveBtn == 'yes') : ?>
        <div class="item">
            <p class="center"><b>Live</b></p>
            <a href="/live">
                <button class="btn-square-green"><img src="resources/icons/camera.svg" class="icon" /></button>
            </a>
            <span class="block center lowopacity">Visualize</span>
        </div>
        <?php
    endif;

    if ($printMotionStartBtn == 'yes') : ?>
        <div id="motion-start-div" class="item">
            <p class="center"><b>Motion</b></p>
            <?php
            if ($motionStatus != 'active') : ?>
                <button id="start-motion-btn" class="btn-square-green" title="Start motion service now">
                    <img src="resources/icons/power.svg" class="icon" />
                </button>
                <span class="block center lowopacity">Start capture</span>
                
                <?php
            endif;
            if ($motionStatus == 'active') {
                echo '<button id="stop-motion-btn" class="btn-square-red" title="Stop motion service"><img src="resources/icons/power.svg" class="icon" /></button>';
                echo '<span class="block center lowopacity">Stop capture</span>';
            } ?>
        </div>
        <?php
    endif;

    /**
     *  Include autostart btn and settings
     */
    if ($printMotionAutostartBtn == 'yes') {
        include_once(ROOT . '/views/includes/motion-autostart-btn.inc.php');
    }
    /**
     *  Include alert btn and settings
     */
    if ($printMotionAlertBtn == 'yes') {
        include_once(ROOT . '/views/includes/motion-alert-btn.inc.php');
    } ?>
</div>