<div id="events-captures-div">

    <h2>Motion: captures</h2>

    <div id="events-captures-container" class="config-div">
        <?php
        $events = $mymotion->getLastEventsFiles();

        if (empty($events)) {
            echo '<p>No files recorded yet.</p>';
        }

        if (!empty($events)) :
            $events = \Controllers\Common::groupBy('Date_start', $events);

            $lastDate = '';

            foreach ($events as $eventDate) {
                echo '<div>';

                foreach ($eventDate as $eventDetails) {
                    $fileId = $eventDetails['File_id'];
                    $filepath = $eventDetails['File'];
                    $date = $eventDetails['Date_start'];
                    $time = $eventDetails['Time_start'];
                    $cameraId = $eventDetails['Camera_id'];

                    if ($lastDate != $date) {
                        echo '<h3>' . $date . '</h3>';
                    } ?>

                    <div>
                        <?php
                        /**
                         *  Case it's an image
                         */
                        if (preg_match('/.jpg$/', $filepath)) : ?>
                            <div>
                                <p><img src="resources/icons/picture.png" class="icon" /><?= $time ?> - JPEG image <img src="resources/icons/play.png" class="icon play-picture-btn" file-id="<?= $fileId ?>" title="Visualize image" /><img src="resources/icons/save.png" class="icon save-picture-btn" file-id="<?= $fileId ?>" title="Download image" /></p>
                            </div>
                            <?php
                        endif;

                        /**
                         *  Case it a movie
                         */
                        if (preg_match('/.mp4$/', $filepath)) : ?>
                            <div>
                                <p><img src="resources/icons/video.png" class="icon" /><?= $time ?> - MP4 video <img src="resources/icons/play.png" class="icon play-video-btn" file-id="<?= $fileId ?>" title="Play video" /><img src="resources/icons/save.png" class="icon save-video-btn" file-id="<?= $fileId ?>" title="Download video" /></p>
                            </div>
                            <!-- <video controls>
                                <source src="" type="video/mp4">
                            </video> -->
                            <?php
                        endif; ?>
                    </div>
                    <?php

                    $lastDate = $date;
                }
                echo '</div>';
            }
        endif ?>
    </div>
</div>