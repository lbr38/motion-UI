<div id="timelapse">
    <div id="timelapse-controls-container">
        <span class="round-btn-tr close-timelapse-btn" title="Close timelapse">
            <img src="/assets/icons/close.svg" />
        </span>
    </div>

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
        $pictures = [];
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
        echo '<div><img id="timelapse-picture" src="/timelapse?id=' . $cameraId . '&picture=' . $picture . '" /></div>';
    } catch (Exception $e) {
        echo '<p>' . $e->getMessage() . '</p>';
    } ?>

    <div id="timelapse-controls" class="flex flex-direction-column row-gap-10 padding-left-15 padding-right-15">
        <div>
            <!-- Slider start and end time -->
            <div class="flex align-item-center justify-space-between">
                <?php
                if (!empty($pictures)) {
                    /**
                     *  Print first picture time
                     */
                    $firstPictureTime = str_replace('.jpg', '', explode('_', $pictures[0])[1]);
                    $firstPictureHour = explode('-', $firstPictureTime)[0];
                    $firstPictureMin = explode('-', $firstPictureTime)[1];
                    $firstPictureSec = explode('-', $firstPictureTime)[2];

                    echo '<div><p class="lowopacity-cst font-size-13">' . $firstPictureHour . ':' . $firstPictureMin . ':' . $firstPictureSec . '</p></div>';

                    /**
                     *  Print current picture time
                     */
                    $pictureTime = str_replace('.jpg', '', explode('_', $picture)[1]);
                    $pictureHour = explode('-', $pictureTime)[0];
                    $pictureMin = explode('-', $pictureTime)[1];
                    $pictureSec = explode('-', $pictureTime)[2];

                    echo '<p id="picture-time" class="font-size-18 text-center lowopacity-cst">' . $pictureHour . ':' . $pictureMin . ':' . $pictureSec . '</p>';

                    /**
                     *  Print last picture time
                     */
                    $lastPictureTime = str_replace('.jpg', '', explode('_', end($pictures))[1]);
                    $lastPictureHour = explode('-', $lastPictureTime)[0];
                    $lastPictureMin = explode('-', $lastPictureTime)[1];
                    $lastPictureSec = explode('-', $lastPictureTime)[2];

                    echo '<div><p class="lowopacity-cst font-size-13">' . $lastPictureHour . ':' . $lastPictureMin . ':' . $lastPictureSec . '</p></div>';
                } ?>
            </div>

            <div class="flex justify-center">
                <!-- Slider -->
                <input id="picture-slider" type="range" min="0" max="0" value="" date="<?= $date ?>" camera-id="<?= $cameraId ?>" />
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

        <div class="flex column-gap-10 align-item-center">
            <div id="timelapse-play-btn" class="round-btn-tr" title="Play timelapse">
                <img src="/assets/icons/play.svg" />
            </div>

            <div id="timelapse-pause-btn" class="round-btn-tr hide" title="Pause timelapse">
                <img src="/assets/icons/pause.svg" />
            </div>

            <div>
                <input id="timelapse-date-input" type="date" class="input-medium" max="<?= date('Y-m-d') ?>" camera-id="<?= $cameraId ?>" value="<?= $date ?>" />
            </div>

            <select id="timelapse-speed-input" class="select-small">
                <option value="500">slow</option>
                <option value="100">medium</option>
                <option value="10" selected>fast</option>
            </select>
        </div>
    </div>
</div>