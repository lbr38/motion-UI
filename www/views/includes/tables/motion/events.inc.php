<div class="reloadable-table" table="<?= $table ?>" offset="<?= $reloadableTableOffset ?>">
    <?php
    if ($reloadableTableTotalItems == 0) {
        echo '<p class="note">No events found for this date.</p>';
    }

    if (!empty($reloadableTableContent)) : ?>
        <div class="flex align-item-center column-gap-10 margin-top-15 margin-bottom-5">
            <p class="font-size-18"><b><?= $eventDateTitle ?></b></p>

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

            /**
             *  Check if current user is allowed to see this camera (only if not admin)
             */
            if (!IS_ADMIN) {
                // If the user has no camera access permissions, skip this camera
                if (empty($permissions['cameras_access'])) {
                    continue;
                }

                // If the user has camera access permissions, but not for this camera, skip this camera
                if (!in_array($cameraId, $permissions['cameras_access'])) {
                    continue;
                }
            }

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
                        <p class="lowopacity-cst font-size-11"><?= $eventTime ?></p>
                    </div>

                    <div class="event-camera-name">
                        <?php
                        if ($cameraId != $lastCameraId) : ?>                            
                            <p class="wordbreakall font-size-13">
                                <b>
                                    <?php
                                    if (!empty($cameraName)) {
                                        echo strtoupper($cameraName);
                                    } else {
                                        echo 'Camera #' . $cameraId;
                                    } ?>
                                </b>
                            </p>
                            <?php
                        endif ?>
                    </div>
                    
                    <div>
                        <div class="event-id flex align-item-center column-gap-5">
                            <?php
                            if ($motionEventId != $lastMotionEventId) : ?>
                                <p class="font-size-13" title="Full event ID #<?= $motionEventId ?>">Event #<?= $motionEventIdShort ?></p>

                                <?php
                                if ($eventSeen != 'true') : ?>
                                    <p>
                                        <code class="bkg-green font-size-10">New</code>
                                    </p>
                                    <?php
                                endif;
                            endif; ?>
                        </div>

                        <div class="event-files-count margin-top-10">
                            <p class="lowopacity-cst"><?= $totalFilesCount ?></p>
                            <img src="/assets/icons/medias.svg" class="mediumopacity-cst icon-np" title="Total media files" />
                        </div>
                    </div>
                </div>

                <div class="flex flex-direction-column row-gap-10 width-100">
                    <div class="flex align-self-end">
                        <input type="checkbox" class="select-all-media-checkbox hide" event-id="<?= $eventId ?>" title="Select all media files" />
                    </div>

                    <div class="event-camera">
                        <?php
                        foreach ($eventFiles as $eventDetails) :
                            $resolution = $eventDetails['Width'] . 'x' . $eventDetails['Height'];
                            if ($resolution == '1280x720') {
                                $resolution = '720p';
                            } else if ($resolution == '1920x1080') {
                                $resolution = '1080p';
                            } else if ($resolution == '2560x1440') {
                                $resolution = '1440p';
                            } else if ($resolution == '3840x2160') {
                                $resolution = '2160p';
                            } else if ($resolution == '7680x4320') {
                                $resolution = '4320p';
                            } ?>
                        
                            <div class="event-row">
                                <?php
                                /**
                                 *  Case it's a picture
                                 */
                                if (preg_match('/\.(jpg|jpeg|png)$/', $eventDetails['File'])) : ?>
                                    <div class="event-media-row">
                                        <div class="event-media">
                                            <?php
                                            if (!file_exists($eventDetails['File'])) { ?>
                                                <div class="file-unavailable">
                                                    <div class="flex flex-direction-column align-item-center row-gap-10">
                                                        <img src="/assets/icons/warning-red.svg" class="icon" />
                                                        <p class="redtext">File deleted</p>
                                                    </div>
                                                </div>
                                                <?php
                                            } elseif (!is_readable($eventDetails['File'])) { ?>
                                                <div class="file-unavailable">
                                                    <div class="flex flex-direction-column align-item-center row-gap-10">
                                                        <img src="/assets/icons/warning.svg" class="icon" />
                                                        <p class="yellowtext">File is unreadable</p>
                                                    </div>
                                                </div>
                                                <?php
                                            } else { ?>
                                                <img src="/media?id=<?= $eventDetails['Id'] ?>" class="play-picture-btn pointer" file-id="<?= $eventDetails['Id'] ?>" title="Visualize picture" />
                                                <?php
                                            } ?>

                                            <div class="event-media-file-number">
                                                <p class="font-size-11">#<?= $fileNumberCounter ?></p>
                                            </div>

                                            <div class="event-media-checkbox-container">
                                                <?php
                                                if (file_exists($eventDetails['File'])) {
                                                    if (is_writeable($eventDetails['File'])) {
                                                        echo '<input type="checkbox" class="event-media-checkbox" file-name="' . basename($eventDetails['File']) . '" file-id="' . $eventDetails['Id'] . '" event-id="' . $eventId . '" title="Select media file" />';
                                                    } else {
                                                        echo '<img src="/assets/icons/warning.svg" class="icon" title="File cannot be selected: not writeable" />';
                                                    }
                                                } ?>
                                            </div>
                                        </div>

                                        <div class="flex align-item-center justify-space-between column-gap-5 lowopacity-cst margin-left-5 margin-right-5 margin-bottom-5 margin-top-5">
                                            <div class="flex align-item-center column-gap-5">
                                                <img src="/assets/icons/picture.svg" class="icon-np" />
                                                <p class="font-size-12" title="Picture file size">(<?= $eventDetails['Size'] ?>)</p>
                                            </div>

                                            <div class="flex flex-wrap align-item-center column-gap-5">
                                                <p class="font-size-12" title="Picture resolution">● <?= $resolution ?></p>
                                                <p class="font-size-12">● <?= $eventDetails['Changed_pixels'] ?> changed pixels</p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                endif;

                                /**
                                 *  Case it a movie
                                 */
                                if (preg_match('/\.(mp4|mkv|mov|webm)$/', $eventDetails['File'])) : ?>
                                    <div class="event-media-row">
                                        <div class="event-media">
                                            <?php
                                            if (!file_exists($eventDetails['File'])) { ?>
                                                <div class="file-unavailable">
                                                    <div class="flex flex-direction-column align-item-center row-gap-10">
                                                        <img src="/assets/icons/warning-red.svg" class="icon" />
                                                        <p class="redtext">File deleted</p>
                                                    </div>
                                                </div>
                                                <?php
                                            } elseif (!is_readable($eventDetails['File'])) { ?>
                                                <div class="file-unavailable">
                                                    <div class="flex flex-direction-column align-item-center row-gap-10">
                                                        <img src="/assets/icons/warning.svg" class="icon" />
                                                        <p class="yellowtext">File is unreadable</p>
                                                    </div>
                                                </div>
                                                <?php
                                            } else { ?>
                                                <video controls preload="metadata" poster="/media?thumbnail&id=<?= $eventDetails['Id'] ?>">
                                                    <source data-src="/media?id=<?= $eventDetails['Id'] ?>">
                                                    <p>Your browser does not support the video.</p>
                                                </video>
                                                <?php
                                            } ?>

                                            <div class="event-media-file-number">
                                                <p class="font-size-13">#<?= $fileNumberCounter ?></p>
                                            </div>

                                            <div class="event-media-checkbox-container">
                                                <?php
                                                if (file_exists($eventDetails['File'])) {
                                                    if (is_writeable($eventDetails['File'])) {
                                                        echo '<input type="checkbox" class="event-media-checkbox" file-name="' . basename($eventDetails['File']) . '" file-id="' . $eventDetails['Id'] . '" event-id="' . $eventId . '" title="Select media file" />';
                                                    } else {
                                                        echo '<img src="/assets/icons/warning.svg" class="icon" title="File cannot be selected: not writeable" />';
                                                    }
                                                } ?>
                                            </div>
                                        </div>

                                        <div class="flex align-item-center column-gap-5 justify-space-between lowopacity-cst margin-left-5 margin-right-5 margin-bottom-5 margin-top-5">
                                            <div class="flex align-item-center column-gap-5">
                                                <img src="/assets/icons/video.svg" class="icon-np" />
                                                <p class="font-size-12" title="Video file size">(<?= $eventDetails['Size'] ?>)</p>
                                            </div>

                                            <div class="flex flex-wrap align-item-center column-gap-5">
                                                <p class="font-size-12" title="Video resolution"><?= $resolution ?></p>
                                                <p class="font-size-12">● <?= $eventDetails['Fps'] ?> FPS</p>
                                                <p class="font-size-12">● <?= $eventDetails['Changed_pixels'] ?> changed pixels</p>
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
