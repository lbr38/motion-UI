<section class="top-buttons margin-top-5 margin-bottom-30 reloadable-container" container="buttons/top">
    <div class="flex column-gap-20">
        <div id="cpu-usage-container" class="flex column-gap-10 align-item-center margin-left-15" title="CPU load">
            <img src="/assets/icons/cpu.svg" class="icon-medium icon-np lowopacity-cst margin-right-0" />
            <img id="cpu-usage-loading" src="/assets/icons/loading.svg" class="mediumopacity-cst icon-medium" />
            <p id="cpu-usage" class="lowopacity-cst font-size-12"></p>
        </div>

        <?php
        if ($serviceStatus == 'running') {
            $iconColor = 'red-blink';
            $title = 'Motion detection is running';
        }
        if ($serviceStatus == 'stopped') {
            $iconColor = 'gray';
            $title = 'Motion detection is stopped';
        } ?>

        <div class="flex column-gap-10 align-item-center" title="<?= $title ?>">
            <img src="/assets/icons/motion.svg" class="icon-medium icon-np lowopacity-cst margin-right-0" />
            <span class="round-item bkg-<?= $iconColor ?>"></span>
        </div>
    </div>
   
    <div class="margin-right-20">
        <div class="relative">
            <?php
            if (IS_ADMIN and NOTIFICATION > 0) : ?>
                <span class="notification-count"><?= NOTIFICATION ?></span>
                <?php
            endif ?>

            <img src="/assets/icons/menu.svg" class="pointer lowopacity get-panel-btn" panel="buttons/menu" title="Open menu" />
        </div>
    </div>

    <script>
        $(document).ready(function () {
            mysystem.getCpuUsage();
        });
    </script>
</section>