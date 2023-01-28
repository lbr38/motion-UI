<section id="top-buttons-section" class="top-buttons">
    <?php
    if (__ACTUAL_URI__ == '/live') : ?>
        <div>
            <a href="/"><img src="resources/icons/back.svg" class="pointer lowopacity" title="Go back" /></a>
        </div>
        <?php
    endif ?>

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
</section>