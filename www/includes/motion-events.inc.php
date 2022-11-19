<div class="main-container">

    <h1>MOTION EVENTS</h1>

    <div id="events-captures-div">
        <?php
        /**
         *  Get date start and end from cookies if there are, else set a default interval of 3 days.
         */
        if (empty($_COOKIE['eventDateStart'])) {
            $eventDateStart = date('Y-m-d', strtotime('-3 day', strtotime(DATE_YMD)));
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

        <div id="motion-events-captures-container">
            <?php
            /**
             *  Print events if there are
             */
            if (empty($eventsDates)) {
                echo '<p>No event recorded yet. Be sure that event registering is setted up in motion\'s configuration file(s).</p>';
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
                                (<?= $totalEventsCount ?> events)
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
                                                $cameraName = $eventDetails['Camera_name'];
                                                $motionEventId = $eventDetails['Motion_id_event'];
                                                $fileId = $eventDetails['FileId'];
                                                $filepath = $eventDetails['File'];
                                                $totalFilesCount = $mymotion->totalFilesByEventId($eventId);

                                                if ($cameraId != $lastCameraId) :
                                                    echo '<div>';

                                                    echo '<p class="event-camera-id"><b>';
                                                    if (!empty($cameraName)) {
                                                        echo $cameraName;
                                                    } else {
                                                        echo 'Camera Id ' . $cameraId;
                                                    }
                                                    echo '</b></p>';
                                                endif; ?>

                                                <div class="event-id">
                                                    <?php
                                                    if ($motionEventId != $lastMotionEventId) {
                                                        echo '<span><br>Event ' . $motionEventId . ' (' . $totalFilesCount . ' files)</span>';
                                                    }

                                                    /**
                                                     *  Case it's an image
                                                     */
                                                    if (preg_match('#\b(.jpg|.webp|.ppm|.grey)\b#', $filepath)) : ?>
                                                        <div class="flex flex-align-itm-center">
                                                            <img src="resources/icons/picture.svg" class="icon" />
                                                            <p>Image</p>
                                                            <?php
                                                            /**
                                                             *  If file exists and is readable
                                                             */
                                                            if (file_exists($filepath)) :
                                                                if (is_readable($filepath)) : ?>
                                                                    <img src="resources/icons/play.svg" class="icon-lowopacity play-image-btn" file-id="<?= $fileId ?>" title="Visualize image" /><img src="resources/icons/save.svg" class="icon-lowopacity save-image-btn" file-id="<?= $fileId ?>" title="Download image" />
                                                                <?php else : ?>
                                                                    <span class="yellowtext"> (permission denied)</span>
                                                                    <?php
                                                                endif ?>
                                                            <?php else : ?>
                                                                <span class="redtext"> (deleted)</span>
                                                                <?php
                                                            endif ?>
                                                        </div>
                                                        <?php
                                                    endif;

                                                    /**
                                                     *  Case it a movie
                                                     */
                                                    if (preg_match('#\b(.avi|.mp4|.swf|.flv|.mov|.mkv)\b#', $filepath)) : ?>
                                                        <div class="flex flex-align-itm-center">
                                                            <img src="resources/icons/video.svg" class="icon" />
                                                            <p>Video</p>
                                                            <?php
                                                            /**
                                                             *  If file exists and is readable
                                                             */
                                                            if (file_exists($filepath)) :
                                                                if (is_readable($filepath)) : ?>
                                                                    <img src="resources/icons/play.svg" class="icon-lowopacity play-video-btn" file-id="<?= $fileId ?>" title="Play video" /><img src="resources/icons/save.svg" class="icon-lowopacity save-video-btn" file-id="<?= $fileId ?>" title="Download video" />
                                                                <?php else : ?>
                                                                    <span class="yellowtext"> (permission denied)</span>
                                                                    <?php
                                                                endif ?>
                                                            <?php else : ?>
                                                                <span class="redtext"> (deleted)</span>
                                                                <?php
                                                            endif ?> 
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
</div>