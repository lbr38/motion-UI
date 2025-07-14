<section class="top-buttons margin-top-5 margin-bottom-30 reloadable-container" container="buttons/top">
    <div class="flex column-gap-20">
        <?php
        if ($load >= 0 && $load < 2) {
            $iconColor = 'green';
        }
        if ($load >= 2 && $load < 3) {
            $iconColor = 'yellow';
        }
        if ($load >= 3) {
            $iconColor = 'red';
        } ?>
    
        <div id="cpu-load" class="flex column-gap-10 align-item-center margin-left-15" title="CPU load">
            <img src="/assets/icons/cpu.svg" class="icon-medium icon-np lowopacity-cst margin-right-0" />
            <span class="lowopacity-cst font-size-12"><?= $load ?></span>
            <span class="round-item bkg-<?= $iconColor ?>"></span>
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
</section>