<div id="timelapse" class="grid justify-items-center row-gap-10">
    <div id="timelapse-picture-container">
        <?php
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        if (!empty($picture)) {
            $picture = $date . '/' . $picture;
        }

        /**
         *  Retrieve timelapse pictures for the specified date
         */
        try {
            /**
             *  Timelapse pictures root directory
             */
            $timelapseDir = CAMERAS_TIMELAPSE_DIR . '/camera-' . $cameraId;

            /**
             *  Check if specified date directory exists
             */
            if (!file_exists($timelapseDir . '/' . $date)) {
                throw new Exception('No timelapse images for this date yet');
            }

            /**
             *  Retrieve pictures name for the specified date
             */
            $pictures = array();
            $picturesGlob = glob($timelapseDir . '/' . $date . '/*.jpg');

            foreach ($picturesGlob as $pictureGlob) {
                $pictures[] = basename($pictureGlob);
            }

            /**
             *  If there are no pictures, throw an exception
             */
            if (empty($pictures)) {
                throw new Exception('No timelapse images for this date yet');
            }

            /**
             *  Print first image first
             */
            if (empty($picture)) {
                $picture = $date . '/' . $pictures[0];
            }

            /**
             *  Print all pictures in a hidden element
             */
            echo '<timelapse-data pictures="' . implode(',', $pictures) . '"></timelapse-data>';

            /**
             *  Print picture
             */
            echo '<img id="timelapse-picture" src="/timelapse?id=' . $cameraId . '&picture=' . $picture . '" />';
        } catch (Exception $e) {
            echo '<p>' . $e->getMessage() . '</p>';
        } ?>
    </div>

    <div class="grid justify-space-between row-gap-10 padding-left-15 padding-right-15">
        <div>
            <?php
            if (!empty($picture)) {
                /**
                 *  Print current picture time
                 */
                $pictureTime = str_replace('.jpg', '', explode('_', $picture)[1]);
                $pictureHour = explode('-', $pictureTime)[0];
                $pictureMin = explode('-', $pictureTime)[1];
                $pictureSec = explode('-', $pictureTime)[2];

                echo '<p id="picture-time" class="font-size-18 text-center lowopacity-cst">' . $pictureHour . ':' . $pictureMin . ':' . $pictureSec . '</p>';
            } ?>
        </div>

        <div>
            <div class="flex justify-center">
                <!-- Slider -->
                <input id="picture-slider" type="range" min="0" max="0" value="" date="<?= $date ?>" camera-id="<?= $cameraId ?>" />
            </div>

            <!-- Slider start and end time -->
            <div class="flex justify-space-between">
                <?php
                if (!empty($pictures)) {
                    /**
                     *  Print first picture time
                     */
                    $firstPictureTime = str_replace('.jpg', '', explode('_', $pictures[0])[1]);
                    $firstPictureHour = explode('-', $firstPictureTime)[0];
                    $firstPictureMin = explode('-', $firstPictureTime)[1];
                    $firstPictureSec = explode('-', $firstPictureTime)[2];

                    echo '<div><p class="lowopacity-cst">' . $firstPictureHour . ':' . $firstPictureMin . ':' . $firstPictureSec . '</p></div>';

                    /**
                     *  Print last picture time
                     */
                    $lastPictureTime = str_replace('.jpg', '', explode('_', end($pictures))[1]);
                    $lastPictureHour = explode('-', $lastPictureTime)[0];
                    $lastPictureMin = explode('-', $lastPictureTime)[1];
                    $lastPictureSec = explode('-', $lastPictureTime)[2];

                    echo '<div><p class="lowopacity-cst">' . $lastPictureHour . ':' . $lastPictureMin . ':' . $lastPictureSec . '</p></div>';
                } ?>
            </div>

            <script>
                $(document).ready(function () {
                    // Get date from the slider
                    var date = $('#picture-slider').attr('date');
                    // Get pictures from pictures array (php)
                    var pictures = <?= json_encode($pictures) ?>;

                    // Update the maximum of the slider to match the number of pictures
                    document.getElementById('picture-slider').max = pictures.length - 1;

                    // Update the displayed image when the slider value changes (event)
                    document.getElementById('picture-slider').addEventListener('input', function(event) {
                        var index = event.target.value;
                        var path = '/timelapse?id=' + <?= $cameraId ?> + '&picture=' + date + '/' + pictures[index];

                        // Retrieve the current picture name
                        picture = pictures[index];

                        // Extract the time from the picture name
                        var time = picture.split('_')[1].split('.')[0];
                        var hour = time.split('-')[0];
                        var min = time.split('-')[1];
                        var sec = time.split('-')[2];

                        // Update the image
                        $('#timelapse-picture').attr('src', path);

                        // Update the picture time
                        $('#picture-time').text(hour + ':' + min + ':' + sec);
                    });
                });
            </script>
        </div>

        <div class="grid grid-2 justify-space-between">
            <div>
                <input id="timelapse-date-input" type="date" class="input-medium" max="<?= date('Y-m-d') ?>" camera-id="<?= $cameraId ?>" value="<?= $date ?>" />
            </div>

            <div class="flex justify-end">
                <div id="timelapse-play-btn" class="slide-btn-medium-tr" title="Play timelapse">
                    <img src="/assets/icons/play.svg" />
                    <span>Play timelapse</span>
                </div>

                <div id="timelapse-pause-btn" class="slide-btn-medium-tr hide" title="Pause timelapse">
                    <img src="/assets/icons/pause.svg" />
                    <span>Pause timelapse</span>
                </div>
            </div>
        </div>

        <div class="flex margin-bottom-40">
            <img src="/assets/icons/close.svg" class="close-timelapse-btn pointer lowopacity" title="Close timelapse screen">
        </div>
    </div>
</div>