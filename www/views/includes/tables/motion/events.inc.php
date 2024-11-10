<div class="reloadable-table" table="<?= $table ?>" offset="<?= $reloadableTableOffset ?>">
    <?php
    if ($reloadableTableTotalItems == 0) {
        echo '<p class="note">No events found for this date.</p>';
    }

    if (!empty($reloadableTableContent)) : ?>
        <div class="flex justify-space-between align-item-center margin-top-15 margin-bottom-5">
            <p class="font-size-18"><b><?= strtoupper($eventDateTitle) ?></b></p>

            <p class="lowopacity-cst">
                <?php
                if ($reloadableTableTotalItems == 1) {
                    echo '(1 event)';
                } else {
                    echo '(' . $reloadableTableTotalItems . ' events)';
                } ?>
            </p>
        </div>

        <?php
        foreach ($reloadableTableContent as $item) :
            /**
             *  Retrieve all files from current event
             */
            $eventId = $item['Id'];
            $eventTime = $item['Time_start'];
            $eventTimeShort = new DateTimeImmutable($eventTime);
            $eventTimeShort = $eventTimeShort->format('H:i');
            $eventStatus = $item['Status'];
            $eventSeen = $item['Seen'];
            $cameraId = $item['Camera_id'];
            $motionEventId = $item['Motion_id_event'];
            $motionEventIdShort = $item['Motion_id_event_short'];
            $lastCameraId = '';
            $lastMotionEventId = '';

            // Get camera name
            $cameraName = $mycamera->getNameById($cameraId);

            // Get total files count
            $totalFilesCount = $mymotionEvent->getTotalFilesByMotionEventId($motionEventId);

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
                <div class="event-metadata">
                    <div class="flex flex-direction-column align-item-center" title="Event start time">
                        <p class="font-size-22"><?= $eventTimeShort ?></p>
                        <p class="note font-size-11"><?= $eventTime ?></p>
                    </div>

                    <div class="event-camera-name-id">
                        <?php
                        if ($cameraId != $lastCameraId) : ?>                            
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
                            <?php
                        endif ?>
                    
                        <div class="event-id">
                            <?php
                            if ($motionEventId != $lastMotionEventId) : ?>
                                <div class="flex column-gap-5 align-item-center">
                                    <p class="note" title="Full event ID #<?= $motionEventId ?>">
                                        <?php
                                        if ($totalFilesCount == 1) {
                                            echo 'Event #' . $motionEventIdShort;
                                        } else {
                                            echo 'Event #' . $motionEventIdShort;
                                        } ?>
                                    </p>

                                    <p>
                                        <?php
                                        if ($eventSeen != 'true') {
                                            echo ' <code class="bkg-green font-size-10">New</code>';
                                        } ?>
                                    </p>
                                </div>

                                <p class="note">
                                    
                                </p>
                                <?php
                            endif; ?>
                        </div>
                    </div>
                </div>

                <div class="flex flex-direction-column row-gap-10 width-100">
                    <div class="flex align-item-center column-gap-5 align-self-end">
                        <p class="note file-counter">
                            <?php
                            if ($totalFilesCount == 1) {
                                echo '1 file';
                            } else {
                                echo $totalFilesCount . ' files';
                            } ?>
                        </p>

                        <input type="checkbox" class="select-all-media-checkbox hide" event-id="<?= $eventId ?>" title="Select event" />
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
                            $imageChangedPixels = $eventDetails['Changed_pixels']; ?>
                        
                            <div class="event-row">
                                <?php
                                /**
                                 *  Case it's a picture
                                 */
                                if (preg_match('#\b(.jpg|.webp|.ppm|.grey)\b#', $filepath)) : ?>
                                    <div class="event-media-row">
                                        <div class="event-media">
                                            <?php
                                            if (file_exists($filepath) and is_readable($filepath)) : ?>
                                                <img src="/media?id=<?= $fileId ?>" class="play-picture-btn pointer" file-id="<?= $fileId ?>" title="Visualize picture" />
                                                <?php
                                            else :
                                                if (!file_exists($filepath)) {
                                                    echo '<div class="file-unavailable"><p class="redtext">File deleted</p></div>';
                                                } elseif (!is_readable($filepath)) {
                                                    echo '<div class="file-unavailable pointer"><p class="yellowtext">File not readable</p></div>';
                                                }
                                            endif ?>

                                            <div class="event-media-file-number">
                                                <p class="font-size-11">#<?= $fileNumberCounter ?></p>
                                            </div>

                                            <div class="event-media-file-type flex align-item-center">
                                                <img src="/assets/icons/picture.svg" class="icon margin-left-5 margin-right-5" />
                                                <span class="font-size-12">(<?= $filesize ?>)</span>
                                            </div>

                                            <div class="event-media-checkbox-container">
                                                <?php
                                                if (file_exists($filepath)) {
                                                    if (is_writeable($filepath)) {
                                                        echo '<input type="checkbox" class="event-media-checkbox" file-name="' . basename($filepath) . '" file-id="' . $fileId . '" event-id="' . $eventId . '" />';
                                                    } else {
                                                        echo '<img src="/assets/icons/warning.svg" class="icon" title="File cannot be selected: not writeable" />';
                                                    }
                                                } ?>
                                            </div>
                                        </div>

                                        <div class="lowopacity-cst flex justify-space-between margin-left-5 margin-right-5 margin-bottom-5 margin-top-5">
                                            <div>
                                                <p class="font-size-12">Width: <?= $imageWidth ?>px</p>
                                                <p class="font-size-12">Height: <?= $imageHeight ?>px</p>
                                            </div>
                                            <div>
                                                <p class="font-size-12 text-right">FPS: <?= $imageFps ?></p>
                                                <p class="font-size-12 text-right">Changed pixels: <?= $imageChangedPixels ?></p>
                                            </div>
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
                                            <div class="event-media">
                                                <?php
                                                if (file_exists($filepath) and is_readable($filepath) and file_exists($filepath . '.thumbnail.jpg') and is_readable($filepath . '.thumbnail.jpg')) : ?>
                                                    <img src="/media?thumbnail&id=<?= $fileId ?>" class="play-video-btn media-thumbnail pointer" file-id="<?= $fileId ?>" title="Play video" onerror="setVideoThumbnailUnavailable(<?= $fileId ?>)" />
                                                    <?php
                                                else :
                                                    if (!file_exists($filepath)) {
                                                        echo '<div class="file-unavailable"><p class="redtext">File deleted</p></div>';
                                                    } elseif (!is_readable($filepath)) {
                                                        echo '<div class="file-unavailable pointer"><p class="yellowtext">File not readable</p></div>';
                                                    } elseif (!file_exists($filepath . '.thumbnail.jpg') or !is_readable($filepath . '.thumbnail.jpg')) {
                                                        echo '<div class="file-unavailable play-video-btn pointer" file-id="' . $fileId . '"><p>Preview unavailable</p></div>';
                                                    }
                                                endif ?>

                                                <div class="event-media-file-number">
                                                    <p class="font-size-13">#<?= $fileNumberCounter ?></p>
                                                </div>

                                                <div class="event-media-file-type flex align-item-center">
                                                    <img src="/assets/icons/video.svg" class="icon margin-left-5 margin-right-5" />
                                                    <span class="font-size-13">(<?= $filesize ?>)</span>
                                                </div>

                                                <div class="event-media-checkbox-container">
                                                    <?php
                                                    if (file_exists($filepath)) {
                                                        if (is_writeable($filepath)) {
                                                            echo '<input type="checkbox" class="event-media-checkbox" file-name="' . basename($filepath) . '" file-id="' . $fileId . '" event-id="' . $eventId . '" />';
                                                        } else {
                                                            echo '<img src="/assets/icons/warning.svg" class="icon" title="File cannot be selected: not writeable" />';
                                                        }
                                                    } ?>
                                                </div>
                                            </div>

                                            <div class="lowopacity-cst flex justify-space-between margin-left-5 margin-right-5 margin-bottom-5 margin-top-5">
                                                <div>
                                                    <p class="font-size-12">Width: <?= $imageWidth ?>px</p>
                                                    <p class="font-size-12">Height: <?= $imageHeight ?>px</p>
                                                </div>
                                                <div>
                                                    <p class="font-size-12 text-right">FPS: <?= $imageFps ?></p>
                                                    <p class="font-size-12 text-right">Changed pixels: <?= $imageChangedPixels ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                endif; ?>
                            </div>

                            <?php
                            $lastCameraId = $cameraId;
                            $lastMotionEventId = $motionEventId;
                            $fileNumberCounter++;
                        endforeach;

                        /**
                         *  If the event is still being processed by motion then print a loading icon
                         */
                        if ($eventStatus != 'done') : ?>
                            <div class="event-row event-loading flex align-item-center justify-center">
                                <img src="/assets/icons/loading.svg" class="icon" title="Processing event" />
                            </div>
                            <?php
                        endif ?>
                    </div>
                </div>
  
            </div>
            <?php
        endforeach; ?>
        
        <div class="flex justify-end">
            <?php \Controllers\Layout\Table\Render::paginationBtn($reloadableTableCurrentPage, $reloadableTableTotalPages); ?>
        </div>

        <?php
    endif ?>
</div>
