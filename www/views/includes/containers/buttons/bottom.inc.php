<section class="bottom-buttons reloadable-container" container="buttons/bottom">
    <?php
    // If user is not admin, display only 'live' and 'events' buttons
    if (!IS_ADMIN) {
        $buttons = array(
            'live' => array(
                'icon' => 'camera.svg',
                'title' => 'Cameras and stream'
            ),
            'events' => array(
                'icon' => 'video.svg',
                'title' => 'Motion events and medias'
            ),
        );

    // If user is admin, display all buttons
    } else {
        $buttons = array(
            'live' => array(
                'icon' => 'camera.svg',
                'title' => 'Cameras and stream'
            ),
            'motion' => array(
                'icon' => 'motion.svg',
                'title' => 'Motion'
            ),
            'events' => array(
                'icon' => 'video.svg',
                'title' => 'Motion events and medias'
            ),
            'stats' => array(
                'icon' => 'stats.svg',
                'title' => 'Motion stats'
            )
        );
    }

    foreach ($buttons as $uri => $properties) :
        $class = '';

        if (__ACTUAL_URI__[1] == '') {
            $actualUri = 'live';
        } else {
            $actualUri = __ACTUAL_URI__[1];
        }

        /**
         *  Set class for current tab
         *  If current tab is not the same as the tab to display, then the icon will be lowopacity
         */
        if ($actualUri != $uri) {
            $class = 'lowopacity';
        } ?>

        <div class="relative">
            <?php
            /**
             *  If current icon to display is 'events' and there are unseen events, display a badge
             */
            if ($uri == 'events' and $unseenEventsTotal > 0) : ?>
                <span class="unseen-events-count"><?= $unseenEventsTotal ?></span>
                <?php
            endif ?>

            <a href="/<?= $uri ?>" onclick="veilBody()">
                <img src="/assets/icons/<?= $properties['icon'] ?>" class="pointer <?= $class ?>" title="<?= $properties['title'] ?>" />
            </a>
        </div>
        <?php
    endforeach; ?>
</section>