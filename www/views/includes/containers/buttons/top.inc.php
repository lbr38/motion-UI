<section class="top-buttons margin-top-5 margin-bottom-30 reloadable-container" container="buttons/top">
    <div id="currentload" class="margin-left-15" title="CPU load">
        <?php
        $load = \Controllers\System::getLoad();

        if ($load >= 0 && $load < 2) {
            $iconColor = 'green';
        }
        if ($load >= 2 && $load < 3) {
            $iconColor = 'yellow';
        }
        if ($load >= 3) {
            $iconColor = 'red';
        } ?>

        <img src="/assets/icons/cpu.svg" class="icon-medium lowopacity-cst" />
        <span class="lowopacity-cst"><?= $load ?></span>
        <span class="round-item bkg-<?= $iconColor ?>"></span>
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