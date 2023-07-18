<section class="main-container reloadable-container" container="motionui/service/status">
    <?php
    /**
     *  Display a warning if motionUI service is not running
     */
    if ($mymotionService->motionuiServiceRunning() !== true) : ?>
        <div class="div-generic-blue">
            <p class="center yellowtext"><img src="assets/icons/warning.png" class="icon" /><b>motionui</b> service is not running. Please restart the docker container.</p>
        </div>
        <?php
    endif ?>
</section>