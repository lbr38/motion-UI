<section class="main-container reloadable-container" container="motion/events/list">
    <div id="events-captures-div">
        <?php \Controllers\Layout\Table\Render::render('motion/events'); ?>

        <div id="motion-events-captures-acquit-container">
            <?php
            if ($unseenEventsTotal > 0) : ?>
                <div class="slide-btn-medium-green acquit-events-btn" title="<?= $_['span']['mark_all_as_seen'] ?>">
                    <img src="/assets/icons/enabled.svg" />
                    <span><?= $_['span']['mark_all_as_seen'] ?></span>
                </div>
                <?php
            endif ?>
        </div>
    </div>
</section>

<div class="event-print-file-container">
    <!-- Event image or video -->
    <div class="event-print-file">
    </div>

    <!-- Close button -->
    <div class="event-print-file-close-btn margin-bottom-30 hide" style="display: block;">
        <img src="/assets/icons/close.svg" class="close-fullscreen-btn pointer lowopacity" title="Close full screen">
    </div>
</div>
