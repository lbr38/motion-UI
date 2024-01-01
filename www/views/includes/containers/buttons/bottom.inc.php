<section class="bottom-buttons reloadable-container" container="buttons/bottom">
    <div>
        <div id="currentload">
            <?php
            $load = \Controllers\System::getLoad();

            if ($load >= 0 && $load < 2) {
                echo '<span class="round-item bkg-green"></span>';
            }
            if ($load >= 2 && $load < 3) {
                echo '<span class="round-item bkg-yellow"></span>';
            }
            if ($load >= 3) {
                echo '<span class="round-item bkg-red"></span>';
            }
            echo ' <span class="lowopacity-cst">CPU load: ' . $load . '</span>'; ?>
        </div>

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

        <?php
        if (__ACTUAL_URI__[1] == '') : ?>
            <div>
                <img src="/assets/icons/cog.svg" class="pointer lowopacity slide-panel-btn" slide-panel="settings" title="Show settings" />
            </div>
            <?php
        endif ?>

        <div>
            <img src="/assets/icons/plus.svg" class="pointer lowopacity slide-panel-btn" slide-panel="new-camera" title="Add a camera" />
        </div>

        <?php
        if (__ACTUAL_URI__[1] == 'live') : ?>
            <div>
                <a href="/"><img src="/assets/icons/back.svg" class="pointer lowopacity" title="Go back" /></a>
            </div>
            <?php
        endif ?>
    </div>
</section>