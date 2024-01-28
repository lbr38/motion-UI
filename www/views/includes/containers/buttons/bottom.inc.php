<section class="bottom-buttons reloadable-container" container="buttons/bottom">
    <?php
    $buttons = array(
        'live' => array(
            'icon' => 'photo-camera.svg',
            'title' => 'Cameras and stream'
        ),
        'motion' => array(
            'icon' => 'power.svg',
            'title' => 'Motion start/stop'
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

    foreach ($buttons as $uri => $properties) :
        if (__ACTUAL_URI__[1] == '') {
            $actualUri = 'live';
        } else {
            $actualUri = __ACTUAL_URI__[1];
        }

        /**
         *  Set class for current tab
         *  If current tab is the same as the actual URI, add class 'current-tab'
         */
        if ($actualUri == $uri) {
            $class = 'relative current-tab';
        } else {
            $class = 'relative';
        } ?>

        <div class="<?= $class ?>">
            <?php
            /**
             *  If current icon to display is 'events' and there are unseen events, display a badge
             */
            if ($uri == 'events' and $unseenEventsTotal > 0) : ?>
                <span class="unseen-events-count"><?= $unseenEventsTotal ?></span>
                <?php
            endif ?>

            <a href="/<?= $uri ?>" onclick="veilBody()">
                <img src="/assets/icons/<?= $properties['icon'] ?>" class="pointer lowopacity" title="<?= $properties['title'] ?>" />
            </a>
        </div>
        <?php
    endforeach; ?>
</section>