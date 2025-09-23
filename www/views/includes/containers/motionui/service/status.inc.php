<section class="main-container reloadable-container" container="motionui/service/status">
    <?php
    /**
     *  Display a warning if motionUI service is not running
     */
    if (!\Controllers\Service\Service::isRunning()) : ?>
        <div class="div-generic-blue flex column-gap-5 align-item-center">
            <img src="/assets/icons/warning.svg" class="icon" />
            <p class="yellowtext">Main service is not running. Please restart the docker container.</p>
        </div>
        <?php
    endif ?>
</section>