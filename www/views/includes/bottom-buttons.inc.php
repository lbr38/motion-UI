<section id="bottom-buttons-section" class="bottom-buttons">
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
            echo ' <span class="lowopacity">CPU load: ' . $load . '</span>'; ?>
        </div>

        <div class="relative">
            <img id="print-notification-btn" src="resources/icons/info.svg" class="pointer lowopacity" title="Show notifications" />
            <?php
            if (NOTIFICATION != 0) : ?>
                <span class="notification-count"><?= NOTIFICATION ?></span>
                <?php
            endif ?>
        </div>

        <div>
            <img id="print-userspace-btn" src="resources/icons/user.svg" class="pointer lowopacity" title="Show userspace" />
        </div>

        <?php
        if (__ACTUAL_URI__ == '/') : ?>
            <div>
                <img id="print-settings-btn" src="resources/icons/cog.svg" class="pointer lowopacity" title="Show settings" />
            </div>
            <?php
        endif ?>

        <div>
            <img id="print-new-camera-btn" src="resources/icons/plus.svg" class="pointer lowopacity" title="Add a camera" />
        </div>

        <?php
        if (__ACTUAL_URI__ == '/live') : ?>
            <div>
                <a href="/"><img src="resources/icons/back.svg" class="pointer lowopacity" title="Go back" /></a>
            </div>
            <?php
        endif ?>
    </div>
</section>