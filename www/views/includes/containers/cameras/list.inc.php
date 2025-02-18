<section class="main-container margin-top-20 reloadable-container" container="cameras/list">
    <?php
    if ($cameraTotal > 0) : ?>
        <div id="live-grid-layout-btns" class="margin-bottom-20">
            <div>
                <div class="grid-icon-2 live-layout-btn pointer lowopacity" columns="2" title="Change grid layout to 2 items per row">
                    <div></div><div></div>
                    <div></div><div></div>
                </div>
                <div class="grid-icon-3 live-layout-btn pointer lowopacity" columns="3" title="Change grid layout to 3 items per row">
                    <div></div><div></div><div></div>
                    <div></div><div></div><div></div>
                    <div></div><div></div><div></div>
                </div>
                <div class="grid-icon-4 live-layout-btn pointer lowopacity" columns="4" title="Change grid layout to 4 items per row">
                    <div></div><div></div><div></div><div></div>
                    <div></div><div></div><div></div><div></div>
                    <div></div><div></div><div></div><div></div>
                    <div></div><div></div><div></div><div></div>
                </div>
            </div>
        </div>
        <?php
    endif ?>

    <div id="camera-grid-container">
        <?php
        $cameraConfigureDiv = '';

        /**
         *  Print cameras if there are
         */
        if ($cameraTotal > 0) :
            $camerasIds = $mycamera->getCamerasIds();

            foreach ($camerasIds as $cameraId) :
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

                /**
                 *  Get camera configuration
                 */
                $camera = $mycamera->getConfiguration($cameraId);

                /**
                 *  Get cameras raw params
                 */
                try {
                    $cameraRawParams = json_decode($camera['Configuration'], true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                    throw new Exception('Error: could not retrieve camera #' . $cameraId . ' configuration: ' . $e->getMessage());
                }

                /**
                 *  Get unseen events count
                 */
                $eventsCount = $mymotionEvent->getUnseenCount($cameraId); ?>

                <div class="camera-container" camera-id="<?= $cameraId ?>">
                    <div class="camera-output">
                        <?php
                        if ($cameraRawParams['stream-enable'] == 'false') : ?>
                            <div class="height-100 flex align-item-center justify-center margin-bottom-30">
                                <div>
                                    <button class="btn-round-none lowopacity-cst"><img src="/assets/icons/close.svg" class="icon" title="Live stream is disabled" /></button>
                                    <p class="block center lowopacity-cst">Live stream is disabled</p>
                                </div>
                            </div>
                            <?php
                        endif;

                        if ($cameraRawParams['stream-enable'] == 'true') : ?>
                            <!-- Unavailable image div -->
                            <div class="camera-unavailable align-item-center row-gap-10 margin-top-15 hide" camera-id="<?= $cameraId ?>">
                                <button class="btn-round-red"><img src="/assets/icons/close.svg" class="icon" title="Unavailable" /></button>
                                <p class="block center lowopacity-cst">Unavailable</p>
                            </div>

                            <!-- Camera stream -->
                            <div class="camera-image relative" camera-id="<?= $cameraId ?>" stream-technology="<?= STREAM_DEFAULT_TECHNOLOGY ?>">
                                <video camera-id="<?= $cameraId ?>" autoplay controls playsinline muted poster="/assets/images/motionui-video-poster.png"></video>

                                <!-- Left and right text / timestamp -->
                                <div class="camera-image-text-left">
                                    <p><b><?= $cameraRawParams['text-left'] ?></b></p>

                                    <?php
                                    // Print timestamp on the right if enabled
                                    if ($cameraRawParams['timestamp-left'] == 'true') {
                                        echo '<p class="camera-image-timestamp font-size-12"></p>';
                                    } ?>
                                </div>

                                <div class="camera-image-text-right">
                                    <p class="text-right"><b><?= $cameraRawParams['text-right'] ?></b></p>

                                    <?php
                                    // Print timestamp on the right if enabled
                                    if ($cameraRawParams['timestamp-right'] == 'true') {
                                        echo '<p class="camera-image-timestamp font-size-12"></p>';
                                    } ?>
                                </div>
                            </div>
                            <?php
                        endif ?>
                    </div>

                    <div class="camera-btn-div flex">
                        <div class="flex justify-space-between align-item-center column-gap-20">
                            <div class="flex flex-direction-column row-gap-2">
                                <p class="wordbreakall font-size-13"><b><?= strtoupper($cameraRawParams['name']) ?></b></p>
                                <p class="mediumopacity-cst font-size-13">
                                    <?php
                                    $type = 'Unknown';
                                    $width = $cameraRawParams['width'];
                                    $height = $cameraRawParams['height'];
                                    $resolution = $width . 'x' . $height;

                                    if (str_contains($cameraRawParams['url'], 'rtsp://')) {
                                        $type = 'RTSP';
                                    }
                                    if (str_contains($cameraRawParams['url'], 'http://') or str_contains($cameraRawParams['url'], 'https://')) {
                                        $type = 'HTTP';
                                    }
                                    if (str_contains($cameraRawParams['url'], 'mjpeg://')) {
                                        $type = 'MJPEG';
                                    }
                                    if (str_contains($cameraRawParams['url'], '/dev/video')) {
                                        $type = 'Local device';
                                    }

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
                                    }

                                    echo $type . ' ● ' . $resolution;

                                    if ($eventsCount > 0) {
                                        if ($eventsCount == 1) {
                                            $eventsCount = '1 new event';
                                        } else {
                                            $eventsCount = $eventsCount . ' new events';
                                        }

                                        echo ' ● <a href="/events" class="font-size-13 yellowtext">' . $eventsCount . '</a>';
                                    } ?>
                                </p>
                            </div>

                            <div class="flex align-item-center column-gap-10">
                                <!-- <span class="round-btn-tr display-ptz-btns" title="Camera PTZ buttons" camera-id="<?= $cameraId ?>">
                                    <img src="/assets/icons/move.svg" />
                                </span> -->

                                <?php
                                if ($cameraRawParams['timelapse-enable'] == 'true') : ?>
                                    <span class="round-btn-tr timelapse-camera-btn" title="Camera timelapse" camera-id="<?= $cameraId ?>">
                                        <img src="/assets/icons/picture.svg" />
                                    </span>
                                    <?php
                                endif;

                                if (IS_ADMIN) : ?>
                                    <span class="round-btn-tr configure-camera-btn" title="Configure camera" camera-id="<?= $cameraId ?>">
                                        <img src="/assets/icons/cog.svg" />
                                    </span>
                                    <?php
                                endif ?>
                            </div>
                        </div>

                        <div class="camera-ptz-btn-container flex-direction-column margin-top-20 hide" camera-id="<?= $cameraId ?>">
                            <div class="flex column-gap-10 align-item-center justify-space-around">
                                <!-- PTZ Continuous move buttons -->
                                <div class="flex flex-direction-column align-item-center justify-center column-gap-10 row-gap-10">
                                    <img src="/assets/icons/top.svg" class="camera-ptz-btn icon-mediumopacity icon-large" title="Move to the top" camera-id="<?= $cameraId ?>" direction="up" move-type="continuous" />
                                    
                                    <div class="flex align-item-center column-gap-10">
                                        <img src="/assets/icons/left.svg" class="camera-ptz-btn icon-mediumopacity icon-large" title="Move to the left" camera-id="<?= $cameraId ?>" direction="left" move-type="continuous" />
                                        <img src="/assets/icons/stop.svg" class="camera-ptz-stop-btn icon-mediumopacity icon-large" title="Stop movement" camera-id="<?= $cameraId ?>" />
                                        <img src="/assets/icons/right.svg" class="camera-ptz-btn icon-mediumopacity icon-large" title="Move to the  right" camera-id="<?= $cameraId ?>" direction="right" move-type="continuous" />
                                    </div>

                                    <img src="/assets/icons/bottom.svg" class="camera-ptz-btn icon-mediumopacity icon-large" title="Move to the bottom" camera-id="<?= $cameraId ?>" direction="down" move-type="continuous" />
                                </div>

                                <!-- PTZ Discontinuous move buttons -->
                                <div class="flex flex-direction-column align-item-center justify-center column-gap-10 row-gap-10">
                                    <img src="/assets/icons/up.svg" class="camera-ptz-btn icon-mediumopacity icon-large" title="Move up" camera-id="<?= $cameraId ?>" direction="up" move-type="discontinuous" />
                                    
                                    <div class="flex align-item-center column-gap-45">
                                        <img src="/assets/icons/previous.svg" class="camera-ptz-btn icon-mediumopacity icon-large" title="Move left" camera-id="<?= $cameraId ?>" direction="left" move-type="discontinuous" />
                                        <img src="/assets/icons/next.svg" class="camera-ptz-btn icon-mediumopacity icon-large" title="Move right" camera-id="<?= $cameraId ?>" direction="right" move-type="discontinuous" />
                                    </div>

                                    <img src="/assets/icons/down.svg" class="camera-ptz-btn icon-mediumopacity icon-large" title="Move down" camera-id="<?= $cameraId ?>" direction="down" move-type="discontinuous" />
                                </div>
                            </div>

                            <div class="flex flex-direction-column align-item-center justify-center margin-top-20">
                                <p class="note">Camera move speed</p>
                                <input type="range" class="camera-ptz-move-speed" min="0.1" max="1" step="0.1" value="0.5" camera-id="<?= $cameraId ?>" />
                            </div>
                        </div>                        
                    </div>
                </div>
                <?php
            endforeach;
        endif ?>

        <?php
        if (IS_ADMIN) : ?>
            <div class="add-camera-container pointer lowopacity get-panel-btn" panel="camera/add" title="Add a camera">
                <img src="/assets/icons/plus.svg" />
                <p class="font-size-18 margin-top-20">Add camera</p>
            </div>
            <?php
        endif ?>
    </div>
</section>
