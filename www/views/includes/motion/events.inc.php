<section class="main-container">
    <?php
    if ($settings['Motion_events'] == 'true') : ?>
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
            $eventsDates = $mymotion->getEventsDate($eventDateStart, $eventDateEnd); ?>

            <div>
                <p class="lowopacity">Period:</p>
                <input type="date" name="dateStart" class="input-medium event-date-input" value="<?= $eventDateStart ?>" />
                <input type="date" name="dateEnd" class="input-medium event-date-input" value="<?= $eventDateEnd ?>" />
            </div>

            <br>

            <button id="event-media-delete-btn" class="btn-medium-red hide">Delete selected</button>

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
                        $totalEventsCount = $mymotion->totalEventByDate($eventDate); ?>

                        <div class="event-date-container">
                            <div class="event-date">
                                <p>
                                    <?php
                                    if ($eventDate == DATE_YMD) {
                                        echo '<b>Today</b>';
                                    } else {
                                        echo '<b>' . $eventDate . '</b>';
                                    } ?>
                                </p>

                                <p class="lowopacity">
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
                             *  Get all times from current date
                             */
                            $eventsTimes = $mymotion->getEventsTime($eventDate);

                            /**
                             *  Print events time from the current date
                             */
                            if (!empty($eventsTimes)) :
                                foreach ($eventsTimes as $eventsTime) :
                                    $eventTime = $eventsTime['Time_start'];
                                    $eventStatus = $eventsTime['Status']; ?>

                                    <div class="div-generic-blue event-container">
                                        <div class="event-time">
                                            <?= $eventTime ?>
                                        </div>

                                        <div class="event-camera">
                                            <?php
                                            /**
                                             *  Get all events in the current date and time
                                             */
                                            $eventsDetails = $mymotion->getEventsDetails($eventDate, $eventTime);

                                            /**
                                             *  Print all events in the current date and time
                                             */
                                            if (!empty($eventsDetails)) :
                                                array_multisort(array_column($eventsDetails, 'Camera_id'), SORT_DESC, array_column($eventsDetails, 'Motion_id_event'), SORT_DESC, $eventsDetails);
                                                $lastCameraId = '';
                                                $lastMotionEventId = '';

                                                foreach ($eventsDetails as $eventDetails) :
                                                    $eventId = $eventDetails['Id'];
                                                    $cameraId = $eventDetails['Camera_id'];
                                                    // $cameraName = $eventDetails['Camera_name'];
                                                    $motionEventId = $eventDetails['Motion_id_event'];
                                                    $fileId = $eventDetails['FileId'];
                                                    $filepath = $eventDetails['File'];
                                                    $filesize = $eventDetails['Size'];
                                                    $totalFilesCount = $mymotion->totalFilesByEventId($eventId);

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
                                                                    <?php
                                                                    if ($totalFilesCount == 1) {
                                                                        echo '<span class="event-id"><br><b>Event #' . $motionEventId . '</b> (1 file)</span>';
                                                                    } else {
                                                                        echo '<span class="event-id"><br><b>Event #' . $motionEventId . '</b> (' . $totalFilesCount . ' files)</span>';
                                                                    } ?>
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
                                                                <div class="flex align-item-center column-gap-4">
                                                                    <?php
                                                                    if ($settings['Motion_events_pictures_thumbnail'] == 'true' and file_exists($filepath) and is_readable($filepath)) : ?>
                                                                        <div class="event-media">
                                                                            <img src="/media?id=<?= $fileId ?>" class="play-picture-btn pointer" file-id="<?= $fileId ?>" title="Visualize picture" />
                                                                            <span class="font-size-13"><img src="resources/icons/picture.svg" class="icon" /> (<?= $filesize ?>)</span>
                                                                        </div>
                                                                        <?php
                                                                    else : ?>
                                                                        <img src="resources/icons/picture.svg" class="icon" />
                                                                        <p>Picture</p>
                                                                        <?php
                                                                        if (file_exists($filepath)) :
                                                                            if (is_readable($filepath)) : ?>
                                                                                <p class="lowopacity font-size-13">(<?= $filesize ?>)</p>
                                                                                <div class="slide-btn play-picture-btn" title="Visualize picture" file-id="<?= $fileId ?>">
                                                                                    <img src="resources/icons/play.svg" />
                                                                                    <span>Visualize picture</span>
                                                                                </div>
                                                                                <?php
                                                                            else : ?>
                                                                            <span class="yellowtext"> (not readable)</span>
                                                                                <?php
                                                                            endif;
                                                                        else : ?>
                                                                            <span class="redtext"> (deleted)</span>
                                                                            <?php
                                                                        endif;
                                                                    endif ?>
                                                                </div>
                                                                <div>
                                                                    <?php
                                                                    if (file_exists($filepath)) {
                                                                        if (is_writeable($filepath)) {
                                                                            echo '<input type="checkbox" class="event-media-checkbox" file-name="' . basename($filepath) . '" file-id="' . $fileId . '" event-id="' . $eventId . '" />';
                                                                        } else {
                                                                            echo '<img src="resources/icons/warning.png" class="icon" title="File cannot be selected: not writeable">';
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
                                                                <div class="flex align-item-center column-gap-4">
                                                                    <?php
                                                                    if ($settings['Motion_events_videos_thumbnail'] == 'true' and (file_exists($filepath) and is_readable($filepath)) and (file_exists($filepath . '.thumbnail.jpg') and is_readable($filepath . '.thumbnail.jpg'))) : ?>
                                                                        <div class="event-media">
                                                                            <img src="/media?thumbnail&id=<?= $fileId ?>" class="play-video-btn pointer" file-id="<?= $fileId ?>" title="Play video" />
                                                                            <span class="font-size-13"><img src="resources/icons/video.svg" class="icon" /> (<?= $filesize ?>)</span>
                                                                        </div>
                                                                        <?php
                                                                    else : ?>
                                                                        <img src="resources/icons/video.svg" class="icon" />
                                                                        <p>Video</p>
                                                                        <?php
                                                                        if (file_exists($filepath)) :
                                                                            if (is_readable($filepath)) : ?>
                                                                                <p class="lowopacity font-size-13">(<?= $filesize ?>)</p>
                                                                                <div class="slide-btn play-video-btn" title="Play video" file-id="<?= $fileId ?>">
                                                                                    <img src="resources/icons/play.svg" />
                                                                                    <span>Play video</span>
                                                                                </div>
                                                                                <?php
                                                                            else : ?>
                                                                            <span class="yellowtext"> (not readable)</span>
                                                                                <?php
                                                                            endif;
                                                                        else : ?>
                                                                            <span class="redtext"> (deleted)</span>
                                                                            <?php
                                                                        endif;
                                                                    endif ?>
                                                                </div>
                                                                <div>
                                                                    <?php
                                                                    if (file_exists($filepath)) {
                                                                        if (is_writeable($filepath)) {
                                                                            echo '<input type="checkbox" class="event-media-checkbox" file-name="' . basename($filepath) . '" file-id="' . $fileId . '" event-id="' . $eventId . '" />';
                                                                        } else {
                                                                            echo '<img src="resources/icons/warning.png" class="icon" title="File cannot be selected: not writeable">';
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
                                                endforeach;
                                            endif ?>
                                        </div>
                                            
                                        <?php
                                        /**
                                         *  If the event is still being processed by motion then print a loading icon
                                         */
                                        if ($eventStatus != 'done') {
                                            echo '<div class="event-running"><img src="resources/icons/loading.gif" class="icon" title="Event is still running" /></div>';
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