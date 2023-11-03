<section class="main-container reloadable-container" container="motion/events/list">
    <?php
    if (MOTION_EVENTS) : ?>
        <h3>MOTION EVENTS</h3>

        <div id="events-captures-div">
            <?php
            /**
             *  Get date start and end from cookies if there are, else set a default interval of 3 days.
             */
            if (empty($_COOKIE['eventDateStart'])) {
                $eventDateStart = date('Y-m-d', strtotime('-1 day', strtotime(DATE_YMD)));
            } else {
                $eventDateStart = $_COOKIE['eventDateStart'];
            }

            if (empty($_COOKIE['eventDateEnd'])) {
                $eventDateEnd = DATE_YMD;
            } else {
                $eventDateEnd = $_COOKIE['eventDateEnd'];
            }

            /**
             *  Get all events dates between selected dates
             */
            $eventsDates = $mymotionEvent->getBetweenDate($eventDateStart, $eventDateEnd); ?>

            <div>
                <p class="lowopacity-cst">Period:</p>
                <input type="date" name="dateStart" class="input-medium event-date-input" value="<?= $eventDateStart ?>" />
                <input type="date" name="dateEnd" class="input-medium event-date-input" value="<?= $eventDateEnd ?>" />
            </div>

            <br>

            <div id="motion-events-captures-container">
                <?php
                /**
                 *  Print events if there are
                 */
                if (empty($eventsDates)) {
                    echo '<p>No event recorded yet.</p>';
                }

                /**
                 *  Print dates
                 */
                if (!empty($eventsDates)) :
                    foreach ($eventsDates as $eventsDate) :
                        $eventDate = $eventsDate['Date_start'];
                        $totalEventsCount = $mymotionEvent->totalEventByDate($eventDate);

                        /**
                         *  $offset defines the start value (offset) of the data to get from the database
                         */
                        $offset = 0;

                        /**
                         *  If a cookie exists for the current date then get the offset value (instead of 0)
                         *  e.g: motion/events/list/2021-01-01/offset
                         */
                        $cookie = 'motion/events/list/' . $eventDate . '/offset';
                        if (!empty($_COOKIE[$cookie])) {
                            $offset = $_COOKIE[$cookie];
                        }

                        /**
                         *  Get all events from the current date
                         */
                        $events = $mymotionEvent->getByDateOffset($eventDate, $offset); ?>

                        <div class="event-date-container" event-date="<?= $eventDate ?>" offset="<?= $offset ?>">
                            <div class="event-date">
                                <p>
                                    <?php
                                    if ($eventDate == DATE_YMD) {
                                        echo '<b>Today</b>';
                                    } else {
                                        echo '<b>' . $eventDate . '</b>';
                                    } ?>
                                </p>

                                <p class="lowopacity-cst">
                                    <?php
                                    if ($totalEventsCount == 1) {
                                        echo '(1 event)';
                                    } else {
                                        echo '(' . $totalEventsCount . ' events)';
                                    } ?>
                                </p>
                            </div>

                            <?php
                            if ($totalEventsCount > 5) : ?>
                                <div class="flex column-gap-10 justify-end">
                                    <?php
                                    if ($offset > 0) {
                                        echo '<span class="round-btn-green event-previous-btn" event-date="' . $eventDate . '" title="Previous events"><img src="/assets/icons/previous.svg"></span>';
                                    }
                                    if ($offset >= 0) {
                                        if ($totalEventsCount > $offset + 5) {
                                            echo '<span class="round-btn-green event-next-btn" event-date="' . $eventDate . '" title="Next events"><img src="/assets/icons/next.svg"></span>';
                                        }
                                    } ?>
                                </div>
                                <br>
                                <?php
                            endif ?>

                            <?php
                            /**
                             *  Print events from the current date
                             */
                            if (!empty($events)) :
                                foreach ($events as $event) :
                                    /**
                                     *  Retrieve all files from current event
                                     */
                                    $eventFiles = $mymotionEvent->getFilesByMotionEventId($event['Motion_id_event']);
                                    $eventId = $event['Id'];
                                    $eventTime = $event['Time_start'];
                                    $eventStatus = $event['Status'];
                                    $cameraId = $event['Camera_id'];
                                    $motionEventId = $event['Motion_id_event'];
                                    $motionEventIdShort = $event['Motion_id_event_short'];
                                    $lastCameraId = '';
                                    $lastMotionEventId = '';

                                    /**
                                     *  Get current event files by motion event id
                                     */
                                    $eventFiles = $mymotionEvent->getFilesByMotionEventId($motionEventId); ?>

                                    <div class="div-generic-blue event-container">
                                        <div class="event-time">
                                            <?= $eventTime ?>
                                        </div>

                                        <div class="event-camera">
                                            <?php
                                            foreach ($eventFiles as $eventDetails) :
                                                $fileId = $eventDetails['Id'];
                                                $filepath = $eventDetails['File'];
                                                $filesize = $eventDetails['Size'];
                                                $imageWidth = $eventDetails['Width'];
                                                $imageHeight = $eventDetails['Height'];
                                                $imageFps = $eventDetails['Fps'];
                                                $imageChangedPixels = $eventDetails['Changed_pixels'];
                                                $totalFilesCount = $mymotionEvent->getTotalFilesByMotionEventId($motionEventId);

                                                /**
                                                 *  Get camera name
                                                 */
                                                $cameraName = $mycamera->getNameById($cameraId);

                                                if ($cameraId != $lastCameraId) :
                                                    /**
                                                     *  div closed by the "if ($cameraId != $lastCameraId)" condition
                                                     */ ?>
                                                    <div>
                                                    <div>
                                                        <p class="event-camera-id">
                                                            <b>
                                                                <?php
                                                                if (!empty($cameraName)) {
                                                                    echo $cameraName;
                                                                } else {
                                                                    echo 'Camera Id #' . $cameraId;
                                                                } ?>
                                                            </b>
                                                        </p>
                                                    </div>
                                                    <?php
                                                endif; ?>

                                                <div class="event-row">
                                                    <?php
                                                    if ($motionEventId != $lastMotionEventId) : ?>
                                                        <div class="flex align-item-center justify-space-between">
                                                            <div>
                                                                <p class="event-id">
                                                                    <br>
                                                                    <?php
                                                                    if ($totalFilesCount == 1) {
                                                                        echo '<span><b>Event #' . $motionEventIdShort . '</b> (1 file)</span>';
                                                                    } else {
                                                                        echo '<span><b>Event #' . $motionEventIdShort . '</b> (' . $totalFilesCount . ' files)</span>';
                                                                    } ?>
                                                                    <br>
                                                                    <span class="lowopacity-cst font-size-11" title="Full event id">#<?= $motionEventId ?>
                                                                </p>
                                                            </div>
                                                            <div>
                                                                <span class="select-all-media-btn lowopacity pointer hide" event-id="<?= $eventId ?>" title="Select all medias">Select all</span>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    endif; ?>

                                                    <?php
                                                    /**
                                                     *  Case it's a picture
                                                     */
                                                    if (preg_match('#\b(.jpg|.webp|.ppm|.grey)\b#', $filepath)) : ?>
                                                        <div class="flex align-item-center justify-space-between event-media-row">
                                                            <div class="flex align-item-center column-gap-20">
                                                                <?php
                                                                if (MOTION_EVENTS_PICTURES_THUMBNAIL === true and file_exists($filepath) and is_readable($filepath)) : ?>
                                                                    <div class="event-media">
                                                                        <img src="/media?id=<?= $fileId ?>" class="play-picture-btn pointer" file-id="<?= $fileId ?>" title="Visualize picture" />
                                                                        <span class="font-size-13"><img src="/assets/icons/picture.svg" class="icon" /> (<?= $filesize ?>)</span>
                                                                    </div>
                                                                    <?php
                                                                else : ?>
                                                                    <div class="flex">
                                                                        <img src="/assets/icons/picture.svg" class="icon" />
                                                                        <div>
                                                                            <p>Picture</p>
                                                                            <?php
                                                                            /**
                                                                             *  Print file size
                                                                             */
                                                                            if (file_exists($filepath) && is_readable($filepath)) : ?>
                                                                                <p class="lowopacity-cst font-size-13">(<?= $filesize ?>)</p>
                                                                                <?php
                                                                            endif ?>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                endif ?>        

                                                                <div class="lowopacity-cst">
                                                                    <p class="font-size-11">Width: <?= $imageWidth ?>px</p>
                                                                    <p class="font-size-11">Height: <?= $imageHeight ?>px</p>
                                                                    <p class="font-size-11">FPS: <?= $imageFps ?></p>
                                                                    <p class="font-size-11">Changed pixels: <?= $imageChangedPixels ?></p>
                                                                </div>

                                                                <?php
                                                                if (file_exists($filepath)) :
                                                                    if (is_readable($filepath)) :
                                                                        if (MOTION_EVENTS_PICTURES_THUMBNAIL !== true) : ?>
                                                                            <div class="slide-btn play-picture-btn" title="Visualize picture" file-id="<?= $fileId ?>">
                                                                                <img src="/assets/icons/play.svg" />
                                                                                <span>Visualize picture</span>
                                                                            </div>
                                                                            <?php
                                                                        endif;
                                                                    else : ?>
                                                                        <span class="yellowtext"> (not readable)</span>
                                                                        <?php
                                                                    endif;
                                                                else : ?>
                                                                    <span class="redtext"> (deleted)</span>
                                                                    <?php
                                                                endif ?>
                                                            </div>

                                                            <div>
                                                                <?php
                                                                if (file_exists($filepath)) {
                                                                    if (is_writeable($filepath)) {
                                                                        echo '<input type="checkbox" class="event-media-checkbox" file-name="' . basename($filepath) . '" file-id="' . $fileId . '" event-id="' . $eventId . '" />';
                                                                    } else {
                                                                        echo '<img src="/assets/icons/warning.png" class="icon" title="File cannot be selected: not writeable">';
                                                                    }
                                                                } ?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    endif;

                                                    /**
                                                     *  Case it a movie
                                                     */
                                                    if (preg_match('#\b(.avi|.mp4|.swf|.flv|.mov|.mkv)\b#', $filepath)) : ?>
                                                        <div class="flex align-item-center justify-space-between event-media-row">
                                                            <div class="flex align-item-center column-gap-20">
                                                                <?php
                                                                if (MOTION_EVENTS_VIDEOS_THUMBNAIL === true and (file_exists($filepath) and is_readable($filepath)) and (file_exists($filepath . '.thumbnail.jpg') and is_readable($filepath . '.thumbnail.jpg'))) : ?>
                                                                    <div class="event-media">
                                                                        <img src="/media?thumbnail&id=<?= $fileId ?>" class="play-video-btn media-thumbnail pointer" file-id="<?= $fileId ?>" title="Play video" onerror="setVideoThumbnailUnavailable(<?= $fileId ?>)" />
                                                                        <span class="font-size-13"><img src="/assets/icons/video.svg" class="icon" /> (<?= $filesize ?>)</span>
                                                                    </div>
                                                                    <?php
                                                                else : ?>
                                                                    <div class="flex">
                                                                        <span><img src="/assets/icons/video.svg" class="icon" /></span>
                                                                        <div>
                                                                            <p>Video</p>
                                                                            <?php
                                                                            /**
                                                                             *  Print file size
                                                                             */
                                                                            if (file_exists($filepath) && is_readable($filepath)) : ?>
                                                                                <p class="lowopacity-cst font-size-13">(<?= $filesize ?>)</p>
                                                                                <?php
                                                                            endif ?>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                endif ?>

                                                                <div class="lowopacity-cst event-media-metadata">
                                                                    <p class="font-size-11">Width: <?= $imageWidth ?>px</p>
                                                                    <p class="font-size-11">Height: <?= $imageHeight ?>px</p>
                                                                    <p class="font-size-11">FPS: <?= $imageFps ?></p>
                                                                    <p class="font-size-11">Changed pixels: <?= $imageChangedPixels ?></p>
                                                                </div>

                                                                <?php
                                                                if (file_exists($filepath)) :
                                                                    if (is_readable($filepath)) :
                                                                        if (MOTION_EVENTS_VIDEOS_THUMBNAIL !== true) : ?>
                                                                            <div class="slide-btn play-video-btn" title="Play video" file-id="<?= $fileId ?>">
                                                                                <img src="/assets/icons/play.svg" />
                                                                                <span>Play video</span>
                                                                            </div>
                                                                            <?php
                                                                        endif;
                                                                    else : ?>
                                                                        <span class="yellowtext"> (not readable)</span>
                                                                        <?php
                                                                    endif;
                                                                else : ?>
                                                                    <span class="redtext"> (deleted)</span>
                                                                    <?php
                                                                endif ?>
                                                            </div>
                                                            
                                                            <div>
                                                                <?php
                                                                if (file_exists($filepath)) {
                                                                    if (is_writeable($filepath)) {
                                                                        echo '<input type="checkbox" class="event-media-checkbox" file-name="' . basename($filepath) . '" file-id="' . $fileId . '" event-id="' . $eventId . '" />';
                                                                    } else {
                                                                        echo '<img src="/assets/icons/warning.png" class="icon" title="File cannot be selected: not writeable">';
                                                                    }
                                                                } ?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    endif; ?>
                                                </div>

                                                <?php
                                                if ($cameraId != $lastCameraId) {
                                                    echo '</div>';
                                                }

                                                $lastCameraId = $cameraId;
                                                $lastMotionEventId = $motionEventId;
                                            endforeach; ?>
                                        </div>
                                            
                                        <?php
                                        /**
                                         *  If the event is still being processed by motion then print a loading icon
                                         */
                                        if ($eventStatus != 'done') {
                                            echo '<div class="event-running"><img src="/assets/icons/loading.gif" class="icon" title="Processing event" /></div>';
                                        } ?>
                                    </div>
                                    <?php
                                endforeach;
                            endif ?>
                        </div>
                        <?php
                    endforeach;
                endif ?>
            </div>
        </div>

        <div id="event-print-file-div">
            <div id="event-print-file-container">
            
                <!-- Event image or video -->
                <div id="event-print-file">
                </div>

                <br>

                <!-- Close button -->
                <span id="event-print-file-close-btn" class="btn-medium-yellow">Close</span>
            </div>
        </div>
        <?php
    endif ?>
</section>