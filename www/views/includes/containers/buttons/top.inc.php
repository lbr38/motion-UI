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
    
        <div class="flex column-gap-10 align-item-center margin-left-15" title="CPU load">
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
            $title = 'Motion service is stopped';
        } ?>

        <div class="flex column-gap-10 align-item-center" title="<?= $title ?>">
            <img src="/assets/icons/motion.svg" class="icon-medium icon-np lowopacity-cst margin-right-0" />
            <span class="round-item bkg-<?= $iconColor ?>"></span>
        </div>
    </div>
   
    <div class="flex column-gap-40 margin-right-15">
        <div class="relative">
            <img src="/assets/icons/alarm.svg" class="pointer lowopacity slide-panel-btn" slide-panel="notification" title="Show notifications" />
            <?php
            if (NOTIFICATION != 0) : ?>
                <span class="notification-count"><?= NOTIFICATION ?></span>
                <?php
            endif ?>
        </div>

        <div>
            <img src="/assets/icons/user.svg" class="pointer lowopacity slide-panel-btn" slide-panel="userspace" title="Show userspace" />
        </div>

        <div>
            <img src="/assets/icons/cog.svg" class="pointer lowopacity slide-panel-btn" slide-panel="settings" title="Show settings" />
        </div>
    </div>
</section>