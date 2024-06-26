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
                 *  Get camera configuration
                 */
                $camera = $mycamera->getConfiguration($cameraId); ?>

                <div class="camera-container" camera-id="<?= $camera['Id'] ?>">
                    <div class="camera-output">
                        <?php
                        if ($camera['Live_enabled'] == 'false') : ?>
                            <div class="flex align-item-center">
                                <div>
                                    <button class="btn-square-none"><img src="/assets/icons/close.svg" class="icon" title="Live stream is disabled" /></button>
                                    <p class="block center lowopacity-cst">Live stream is disabled</p>
                                </div>
                            </div>
                            <?php
                        endif;

                        if ($camera['Live_enabled'] == 'true') :
                            /**
                             *  If camera is an RTSP camera, print unsupported stream message
                             */
                            if (str_starts_with($camera['Url'], 'rtsp://')) : ?>
                                <div class="flex align-item-center">
                                    <div>
                                        <button class="btn-square-none"><img src="/assets/icons/close.svg" class="icon" title="Unsupported stream" /></button>
                                        <p class="block center lowopacity-cst">RTSP stream cannot be displayed through web browsers</p>
                                    </div>
                                </div>
                                <?php
                            /**
                             *  Else print camera stream or image
                             */
                            else : ?>
                                <!-- Loading image -->
                                <div class="camera-loading">
                                    <button class="btn-square-none"><img src="/assets/images/loading.gif" class="icon" title="Loading image" /></button>
                                    <span class="block center lowopacity-cst">Loading image</span>
                                </div>

                                <!-- Unavailable image div -->
                                <div class="camera-unavailable flex align-item-center hide" camera-id="<?= $camera['Id'] ?>">
                                    <div>
                                        <button class="btn-square-red"><img src="/assets/icons/close.svg" class="icon" title="Unavailable" /></button>
                                        <span class="block center lowopacity-cst">Unavailable</span>
                                    </div>
                                </div>

                                <!-- Camera image -->
                                <div class="camera-image relative hide" camera-id="<?= $camera['Id'] ?>">
                                    <?php
                                    /**
                                     *  Type 'image'
                                     */
                                    if ($camera['Output_type'] == 'image') : ?>
                                        <img loading="lazy" src="/assets/icons/photo-camera.svg" data-src="/image?id=<?= $camera['Id'] ?>" camera-type="image" camera-id="<?= $camera['Id'] ?>" camera-refresh="<?= $camera['Refresh'] ?>" refresh-timestamp="" style="transform:rotate(<?= $camera['Rotate'] ?>deg);" class="full-screen-camera-btn pointer" title="Click to full screen" onerror="setUnavailable(<?= $camera['Id'] ?>)">
                                        <?php
                                    endif;

                                    /**
                                     *  Type 'video'
                                     */
                                    if ($camera['Output_type'] == 'video') : ?>
                                        <img loading="lazy" src="/assets/icons/photo-camera.svg" data-src="/stream?id=<?= $camera['Id'] ?>" camera-type="video" camera-id="<?= $camera['Id'] ?>" style="transform:rotate(<?= $camera['Rotate'] ?>deg);" class="full-screen-camera-btn pointer" title="Click to full screen" onerror="setUnavailable(<?= $camera['Id'] ?>)">
                                        <?php
                                    endif ?>

                                    <!-- Left and right text / timestamp -->
                                    <div class="camera-image-text-left">
                                        <p><b><?= $camera['Text_left'] ?></b></p>

                                        <?php
                                        /**
                                         *  Print timestamp on the right if enabled
                                         */
                                        if ($camera['Timestamp_left'] == 'true') {
                                            echo '<p class="camera-image-timestamp font-size-12"></p>';
                                        } ?>
                                    </div>

                                    <div class="camera-image-text-right">
                                        <p class="text-right"><b><?= $camera['Text_right'] ?></b></p>

                                        <?php
                                        /**
                                         *  Print timestamp on the right if enabled
                                         */
                                        if ($camera['Timestamp_right'] == 'true') {
                                            echo '<p class="camera-image-timestamp font-size-12"></p>';
                                        } ?>
                                    </div>
                                </div>
                                <?php
                            endif;
                        endif ?>
                    </div>

                    <div class="camera-btn-div flex">
                        <div>
                            <img src="/assets/icons/close.svg" class="close-full-screen-camera-btn pointer lowopacity hide" camera-id="<?= $camera['Id'] ?>" title="Close full screen" />
                        </div>

                        <div class="flex align-item-center justify-space-between">
                            <div>
                                <p class="label-green"><b><?= $camera['Name'] ?></b></p>
                            </div>
                            <div>
                                <div class="slide-btn timelapse-camera-btn" title="See timelapse" camera-id="<?= $camera['Id'] ?>">
                                    <img src="/assets/icons/picture.svg" />
                                    <span>Timelapse</span>
                                </div>

                                <div class="slide-btn configure-camera-btn" title="Configure" camera-id="<?= $camera['Id'] ?>">
                                    <img src="/assets/icons/cog.svg" />
                                    <span>Configure</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            endforeach;
        endif ?>

        <div class="add-camera-container pointer lowopacity slide-panel-btn" slide-panel="new-camera" title="Add a camera">
            <img src="/assets/icons/plus.svg" />
            <p class="font-size-18 margin-top-20">Add camera</p>
        </div>
    </div>

</section>
