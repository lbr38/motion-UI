<section class="main-container reloadable-container" container="motion/events/list">
    <div id="events-captures-div">
        <?php
        /**
         *  Get date from cookies if there are, else set today's date
         */
        if (empty($_COOKIE['event-date'])) {
            $eventDate = DATE_YMD;
        } else {
            $eventDate = $_COOKIE['event-date'];
        } ?>

        <h6>SELECT DATE</h6>
        <div>
            <div>
                <div class="flex column-gap-10">
                    <input type="date" class="input-medium event-date-input" value="<?= $eventDate ?>" />
                </div>
            </div>
        </div>

        <?php \Controllers\Layout\Table\Render::render('motion/events'); ?>

        <div id="motion-events-captures-acquit-container">
            <?php
            if ($unseenEventsTotal > 0) : ?>
                <div class="slide-btn-medium-green acquit-events-btn" title="Mark all events as seen">
                    <img src="/assets/icons/enabled.svg" />
                    <span>Mark all events as seen</span>
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
