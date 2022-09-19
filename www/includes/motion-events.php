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
     *  Get events between selected dates
     */
    $events = $mymotion->getEvents($eventDateStart, $eventDateEnd); ?>

    <h2>Motion: events</h2>

    <div>
        <form id="eventDateForm" autocomplete="off">
            <input type="date" name="dateStart" class="input-small" value="<?= $eventDateStart ?>" />
            <input type="date" name="dateEnd" class="input-small" value="<?= $eventDateEnd ?>" />

            <button type="submit" class="btn-small-green">Show</button>
        </form>
    </div>

    <div id="events-captures-container" class="config-div">
        <?php
        /**
         *  Print events if there are
         */
        if (empty($events)) {
            echo '<p>No event files recorded yet.</p>';
        }

        if (!empty($events)) :
            $events = \Controllers\Common::groupBy('Date_start', $events);

            $lastDate = '';
            $lastEventId = '';

            foreach ($events as $eventDate) {
                echo '<div>';

                foreach ($eventDate as $eventDetails) {
                    $eventId = $eventDetails['Id'];
                    $eventMotionId = $eventDetails['Motion_id_event'];
                    $eventStatus = $eventDetails['Status'];
                    $fileId = $eventDetails['File_id'];
                    $filepath = $eventDetails['File'];
                    $date = $eventDetails['Date_start'];
                    $time = $eventDetails['Time_start'];
                    $cameraId = $eventDetails['Camera_id'];
                    $cameraName = $eventDetails['Camera_name'];

                    /**
                     *  Print date if not already printed
                     */
                    if ($lastDate != $date) {
                        /**
                         *  Get total event count for this date
                         */
                        $totalEventsCount = $mymotion->getDailyEventCount($date);

                        echo '<h3>' . $date . ' (' . $totalEventsCount . ' events)</h3>';
                    }

                    /**
                     *  Print event Id if not already printed
                     */
                    if ($lastEventId != $eventId) {
                        /**
                         *  Get total files count for this event
                         */
                        $totalFilesCount = $mymotion->getEventFileCount($eventId);

                        echo '<p><br><b>' . $time . ' - ';

                        if (!empty($cameraName)) {
                            echo $cameraName;
                        } else {
                            echo 'Camera Id ' . $cameraId;
                        }

                        echo ' - Event ' . $eventMotionId . '</b> (' . $totalFilesCount . ' files)';

                        /**
                         *  If the event is still being processed by motion then print a message
                         */
                        if ($eventStatus != 'done') {
                            echo ' <span class="yellowtext">(still running)</span>';
                        }

                        echo '</p>';
                    } ?>

                    <div>
                        <?php
                        /**
                         *  Case it's an image
                         */
                        if (preg_match('#\b(.jpg|.webp|.ppm|.grey)\b#', $filepath)) : ?>
                            <div>
                                <p>
                                    <img src="resources/icons/picture.svg" class="icon" />Image
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
                                </p>
                            </div>
                            <?php
                        endif;

                        /**
                         *  Case it a movie
                         */
                        if (preg_match('#\b(.avi|.mp4|.swf|.flv|.mov|.mkv)\b#', $filepath)) : ?>
                            <div>
                                <p>
                                    <img src="resources/icons/video.svg" class="icon" />Video
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
                                </p>
                            </div>
                            <?php
                        endif; ?>
                    </div>
                    <?php

                    $lastDate = $date;
                    $lastEventId = $eventId;
                }
                echo '</div>';
            }
        endif ?>
    </div>
</div>