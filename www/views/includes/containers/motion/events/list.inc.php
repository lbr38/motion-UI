<section class="main-container reloadable-container" container="motion/events/list">
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

        <p class="lowopacity-cst">Period:</p>

        <div class="flex justify-space-between align-item-center">
            <div>
                <div class="flex column-gap-10">
                    <input type="date" name="dateStart" class="input-medium event-date-input" value="<?= $eventDateStart ?>" />
                    <input type="date" name="dateEnd" class="input-medium event-date-input" value="<?= $eventDateEnd ?>" />
                </div>
            </div>

            <div>
                <div class="slide-btn acquit-events-btn" title="Mark all events as seen">
                    <img src="/assets/icons/enabled.svg" />
                    <span>Mark all events as seen</span>
                </div>
            </div>
        </div>

        <br>

        <div id="motion-events-captures-container">
            <?php
            /**
             *  Print events if there are
             */
            if (empty($eventsDates)) {
                echo '<p class="lowopacity-cst">No motion event or media recorded for this period</p>';
            }

            /**
             *  Print dates
             */
            if (!empty($eventsDates)) :
                foreach ($eventsDates as $eventsDate) :
                    $eventDate = $eventsDate['Date_start'];
                    $totalEventsCount = count($mymotionEvent->getByDate($eventDate));

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
                    $events = $mymotionEvent->getByDate($eventDate, true, $offset); ?>

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
                        /**
                         *  Pagination buttons
                         */

                        /**
                         *  Calculate total pages and current page
                         */
                        $totalPages = ceil($totalEventsCount / 5);
                        $currentPage = ceil($offset / 5) + 1;

                        /**
                         *  Print pagination if more than one page
                         */
                        if ($totalPages > 1) :
                            $output = ''; ?>

                            <div class="flex justify-end margin-bottom-15">
                                <?php
                                /**
                                 *  Previous button
                                 */
                                if ($currentPage > 1) {
                                    $output .= '<button class="event-pagination-btn pagination-btn-first pagination-btn-previous" page="' . ($currentPage - 1) . '" event-date="' . $eventDate . '" title="Previous"><img src="/assets/icons/previous.svg" class="icon" /></button>';
                                }

                                /**
                                 *  Button 1
                                 */
                                if ($currentPage == 1) {
                                    $output .= '<button class="event-pagination-btn pagination-btn-first pagination-btn-current" page="1" event-date="' . $eventDate . '">1</button>';
                                } else {
                                    $output .= '<button class="event-pagination-btn pagination-btn" page="1" event-date="' . $eventDate . '">1</button>';
                                }

                                /**
                                 *  Print 2 previous and next pages
                                 */
                                $start = max(2, $currentPage - 1);
                                $end = min($totalPages - 1, $currentPage + 1);

                                if ($start > 2) {
                                    $output .= '<span class="pagination-btn">...</span>';
                                }

                                for ($i = $start; $i <= $end; $i++) {
                                    if ($currentPage == $i) {
                                        $output .= '<button class="event-pagination-btn pagination-btn pagination-btn-current" page="' . $i . '" event-date="' . $eventDate . '">' . $i . '</button>';
                                    } else {
                                        $output .= '<button class="event-pagination-btn pagination-btn" page="' . $i . '" event-date="' . $eventDate . '">' . $i . '</button>';
                                    }
                                }

                                if ($end < $totalPages - 1) {
                                    $output .= '<span class="pagination-btn">...</span>';
                                }

                                /**
                                 *  Button last
                                 */
                                if ($totalPages > 1) {
                                    if ($currentPage == $totalPages) {
                                        $output .= '<button class="event-pagination-btn pagination-btn-last pagination-btn-current" page="' . $totalPages . '" event-date="' . $eventDate . '">' . $totalPages . '</button>';
                                    } else {
                                        $output .= '<button class="event-pagination-btn pagination-btn" page="' . $totalPages . '" event-date="' . $eventDate . '">' . $totalPages . '</button>';
                                    }
                                }

                                /**
                                 *  Next button
                                 */
                                if ($currentPage < $totalPages) {
                                    $output .= '<button class="event-pagination-btn pagination-btn-last pagination-btn-next" page="' . ($currentPage + 1) . '" event-date="' . $eventDate . '" title="Next"><img src="/assets/icons/next.svg" class="icon" /></button>';
                                }

                                echo $output; ?>
                            </div>
                            <?php
                        endif ?>

                        <div class="events-container">
                            <?php
                            /**
                             *  Print events from the current date
                             */
                            if (!empty($events)) :
                                foreach ($events as $event) :
                                    /**
                                     *  Retrieve all files from current event
                                     */
                                    $eventId = $event['Id'];
                                    $eventTime = $event['Time_start'];
                                    $eventTimeShort = new DateTimeImmutable($eventTime);
                                    $eventTimeShort = $eventTimeShort->format('H:i');
                                    $eventStatus = $event['Status'];
                                    $eventSeen = $event['Seen'];
                                    $cameraId = $event['Camera_id'];
                                    $motionEventId = $event['Motion_id_event'];
                                    $motionEventIdShort = $event['Motion_id_event_short'];
                                    $lastCameraId = '';
                                    $lastMotionEventId = '';

                                    /**
                                     *  File number counter
                                     *  This will be used to number the files in the event
                                     */
                                    $fileNumberCounter = 1;

                                    /**
                                     *  Get current event files by motion event id
                                     */
                                    $eventFiles = $mymotionEvent->getFilesByMotionEventId($motionEventId);

                                    /**
                                     *  Set avent as 'seen' now that it's displayed
                                     */
                                    $mymotionEvent->seen($eventId); ?>

                                    <div class="div-generic-blue event-container veil-on-reload">
                                        <div class="event-time" title="Event start time">
                                            <p><?= $eventTimeShort ?></p>
                                            <span class="lowopacity-cst font-size-11"><?= $eventTime ?></span>
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
                                                        <p class="label-green">
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
                                                                    <span>
                                                                        <?php
                                                                        $eventSeen;

                                                                        if ($totalFilesCount == 1) {
                                                                            echo '<b>Event #' . $motionEventIdShort . '</b> (1 file)';
                                                                        } else {
                                                                            echo '<b>Event #' . $motionEventIdShort . '</b> (' . $totalFilesCount . ' files)';
                                                                        } ?>
                                                                    </span>

                                                                    <span>
                                                                        <?php
                                                                        if ($eventSeen != 'true') {
                                                                            echo '<code class="bkg-green">New</code>';
                                                                        } ?>
                                                                    </span>
                                                                    <br>
                                                                    <span class="lowopacity-cst font-size-11" title="Full event id">#<?= $motionEventId ?>
                                                                </p>
                                                            </div>
                                                            <div>
                                                                <span class="select-all-media-btn lowopacity padding-right-15 pointer hide" event-id="<?= $eventId ?>" title="Select all medias">Select all</span>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    endif; ?>

                                                    <?php
                                                    /**
                                                     *  Case it's a picture
                                                     */
                                                    if (preg_match('#\b(.jpg|.webp|.ppm|.grey)\b#', $filepath)) : ?>
                                                        <div class="event-media-row">
                                                            <div>
                                                                <?php
                                                                /**
                                                                 *  Case it's a picture and the thumbnail option is enabled
                                                                 */
                                                                if (MOTION_EVENTS_PICTURES_THUMBNAIL) : ?>
                                                                    <div class="event-media">
                                                                        <?php
                                                                        if (file_exists($filepath) and is_readable($filepath)) : ?>
                                                                            <img src="/media?id=<?= $fileId ?>" class="play-picture-btn pointer" file-id="<?= $fileId ?>" title="Visualize picture" />
                                                                            <?php
                                                                        else :
                                                                            if (!file_exists($filepath)) {
                                                                                echo '<div class="file-unavailable"><p class="redtext">File<br>deleted</p></div>';
                                                                            } elseif (!is_readable($filepath)) {
                                                                                echo '<div class="file-unavailable pointer"><p class="yellowtext">File not<br>readable</p></div>';
                                                                            }
                                                                        endif ?>

                                                                        <div class="event-media-file-number">
                                                                            <p class="font-size-11">#<?= $fileNumberCounter ?></p>
                                                                        </div>

                                                                        <div class="event-media-file-type flex align-item-center">
                                                                            <img src="/assets/icons/video.svg" class="icon" />
                                                                            <span class="font-size-12">(<?= $filesize ?>)</span>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                endif;

                                                                /**
                                                                 *  Case it's a picture and the thumbnail option is disabled
                                                                 */
                                                                if (!MOTION_EVENTS_PICTURES_THUMBNAIL) : ?>
                                                                    <div class="flex align-item-center justify-space-between padding-left-15 padding-right-15">
                                                                        <div>
                                                                            <div class="flex align-item-center">
                                                                                <img src="/assets/icons/picture.svg" class="icon" />
                                                                                <p class="font-size-12">Picture #<?= $fileNumberCounter ?></p>
                                                                            </div>
                                                                            <?php
                                                                            /**
                                                                             *  Print file size
                                                                             */
                                                                            if (file_exists($filepath) && is_readable($filepath)) : ?>
                                                                                <p class="lowopacity-cst font-size-13">(<?= $filesize ?>)</p>
                                                                                <?php
                                                                            else :
                                                                                if (!file_exists($filepath)) {
                                                                                    echo '<span class="redtext"> (deleted)</span>';
                                                                                } elseif (!is_readable($filepath)) {
                                                                                    echo '<span class="yellowtext"> (not readable)</span>';
                                                                                }
                                                                            endif ?>
                                                                        </div>

                                                                        <?php
                                                                        if (is_readable($filepath)) : ?>
                                                                            <span class="round-btn-green play-picture-btn" file-id="<?= $fileId ?>" title="Visualize picture">
                                                                                <img src="/assets/icons/play.svg" />
                                                                            </span>
                                                                            <?php
                                                                        endif ?>
                                                                    </div>
                                                                    <?php
                                                                endif ?>

                                                                <div class="lowopacity-cst event-media-metadata">
                                                                    <p class="font-size-11">Width: <?= $imageWidth ?>px</p>
                                                                    <p class="font-size-11">Height: <?= $imageHeight ?>px</p>
                                                                    <p class="font-size-11">FPS: <?= $imageFps ?></p>
                                                                    <p class="font-size-11">Changed pixels: <?= $imageChangedPixels ?></p>
                                                                </div>
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
                                                        <div class="event-media-row">
                                                            <div>
                                                                <?php
                                                                /**
                                                                 *  Case it's a movie and the thumbnail option is enabled
                                                                 */
                                                                if (MOTION_EVENTS_VIDEOS_THUMBNAIL) : ?>
                                                                    <div class="event-media">
                                                                        <?php
                                                                        if (file_exists($filepath) and is_readable($filepath) and file_exists($filepath . '.thumbnail.jpg') and is_readable($filepath . '.thumbnail.jpg')) : ?>
                                                                            <img src="/media?thumbnail&id=<?= $fileId ?>" class="play-video-btn media-thumbnail pointer" file-id="<?= $fileId ?>" title="Play video" onerror="setVideoThumbnailUnavailable(<?= $fileId ?>)" />
                                                                            <?php
                                                                        else :
                                                                            if (!file_exists($filepath)) {
                                                                                echo '<div class="file-unavailable"><p class="redtext">File<br>deleted</p></div>';
                                                                            } elseif (!is_readable($filepath)) {
                                                                                echo '<div class="file-unavailable pointer"><p class="yellowtext">File not<br>readable</p></div>';
                                                                            } elseif (!file_exists($filepath . '.thumbnail.jpg') or !is_readable($filepath . '.thumbnail.jpg')) {
                                                                                echo '<div class="file-unavailable play-video-btn pointer" file-id="' . $fileId . '"><p>Preview<br>unavailable</p></div>';
                                                                            }
                                                                        endif ?>

                                                                        <div class="event-media-file-number">
                                                                            <p class="font-size-11">#<?= $fileNumberCounter ?></p>
                                                                        </div>

                                                                        <div class="event-media-file-type flex align-item-center">
                                                                            <img src="/assets/icons/video.svg" class="icon" />
                                                                            <span class="font-size-12">(<?= $filesize ?>)</span>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                endif;

                                                                /**
                                                                 *  Case it's a movie and the thumbnail option is disabled
                                                                 */
                                                                if (!MOTION_EVENTS_VIDEOS_THUMBNAIL) :?>
                                                                    <div class="flex align-item-center justify-space-between padding-left-15 padding-right-15">
                                                                        <div>
                                                                            <div class="flex align-item-center">
                                                                                <img src="/assets/icons/video.svg" class="icon" />
                                                                                <p class="font-size-12">Video #<?= $fileNumberCounter ?></p>
                                                                            </div>
                                                                            <?php
                                                                            /**
                                                                             *  Print file size
                                                                             */
                                                                            if (file_exists($filepath) && is_readable($filepath)) : ?>
                                                                                <p class="lowopacity-cst font-size-13">(<?= $filesize ?>)</p>
                                                                                <?php
                                                                            else :
                                                                                if (!file_exists($filepath)) {
                                                                                    echo '<span class="redtext"> (deleted)</span>';
                                                                                } elseif (!is_readable($filepath)) {
                                                                                    echo '<span class="yellowtext"> (not readable)</span>';
                                                                                }
                                                                            endif ?>
                                                                        </div>
                                                                        <?php
                                                                        if (is_readable($filepath)) : ?>
                                                                            <span class="round-btn-green play-video-btn" file-id="<?= $fileId ?>" title="Play video">
                                                                                <img src="/assets/icons/play.svg" />
                                                                            </span>
                                                                            <?php
                                                                        endif ?>
                                                                    </div>
                                                                    <?php
                                                                endif ?>

                                                                <div class="lowopacity-cst event-media-metadata">
                                                                    <p class="font-size-11">Width: <?= $imageWidth ?>px</p>
                                                                    <p class="font-size-11">Height: <?= $imageHeight ?>px</p>
                                                                    <p class="font-size-11">FPS: <?= $imageFps ?></p>
                                                                    <p class="font-size-11">Changed pixels: <?= $imageChangedPixels ?></p>
                                                                </div>
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

                                                $fileNumberCounter++;
                                            endforeach; ?>
                                        </div>
                                            
                                        <?php
                                        /**
                                         *  If the event is still being processed by motion then print a loading icon
                                         */
                                        if ($eventStatus != 'done') {
                                            echo '<div class="event-running"><img src="/assets/images/loading.gif" class="icon" title="Processing event" /></div>';
                                        } ?>
                                    </div>
                                    <?php
                                endforeach;
                            endif ?>
                        </div>
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
</section>