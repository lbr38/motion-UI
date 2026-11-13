<section class="bottom-buttons reloadable-container" container="buttons/bottom">
    <?php
    // If user is not admin, display only 'live' and 'events' buttons
    if (!IS_ADMIN) {
        $buttons = [
            'live' => [
                'icon' => 'videocam.svg',
                'title' => __('Cameras and stream')
            ],
            'events' => [
                'icon' => 'video.svg',
                'title' => __('Motion events')
            ]
        ];

    // If user is admin, display all buttons
    } else {
        $buttons = [
            'live' => [
                'icon' => 'videocam.svg',
                'title' => __('Cameras and stream')
            ],
            'motion' => [
                'icon' => 'motion.svg',
                'title' => __('Motion detection')
            ],
            'events' => [
                'icon' => 'video.svg',
                'title' => __('Motion events')
            ],
            'stats' => [
                'icon' => 'stats.svg',
                'title' => __('Stats')
            ]
        ];
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

            <a href="/<?= $uri ?>" onclick="mylayout.veilBody()">
                <div class="flex align-item-center column-gap-10 <?= $class ?>" title="<?= $properties['title'] ?>">
                    <img src="/assets/icons/<?= $properties['icon'] ?>" class="pointer" title="<?= $properties['title'] ?>" />
                    <p class="bottom-buttons-title hide font-size-13"><b><?= strtoupper($properties['title']) ?></b></p>
                </div>
            </a>
        </div>
        <?php
    endforeach; ?>
</section>