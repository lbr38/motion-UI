<section id="motionui-service-section">
    <?php
    /**
     *  Display a warning if motionUI service is not running
     */
    if ($mymotion->motionuiServiceRunning() !== true) {
        echo '<p class="center yellowtext"><img src="resources/icons/warning.png" class="icon" /><b>motionui</b> service is not running. Please start it.</p>';
    } ?>
</section>