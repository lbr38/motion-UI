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
                                    <button class="btn-square-none lowopacity-cst"><img src="/assets/icons/close.svg" class="icon" title="Live stream is disabled" /></button>
                                    <p class="block center lowopacity-cst">Live stream is disabled</p>
                                </div>
                            </div>
                            <?php
                        endif;

                        if ($camera['Live_enabled'] == 'true') : ?>
                            <!-- Loading image -->
                            <div class="camera-loading" camera-id="<?= $camera['Id'] ?>">
                                <button class="btn-square-none"><img src="/assets/icons/loading.svg" class="icon" title="Loading image" /></button>
                                <p class="block center lowopacity-cst">Loading image</p>
                            </div>

                            <!-- Unavailable image div -->
                            <div class="camera-unavailable flex align-item-center row-gap-10 margin-top-15 hide" camera-id="<?= $camera['Id'] ?>">
                                <button class="btn-square-red"><img src="/assets/icons/close.svg" class="icon" title="Unavailable" /></button>
                                <p class="block center lowopacity-cst">Unavailable</p>
                            </div>

                            <!-- Camera image -->
                            <div class="camera-image relative hide" camera-id="<?= $camera['Id'] ?>">
                                <!-- <img loading="lazy" src="/assets/icons/photo-camera.svg" data-src="/stream?id=<?= $camera['Id'] ?>&<?= time() ?>" camera-type="video" camera-id="<?= $camera['Id'] ?>" class="full-screen-camera-btn pointer" title="Click to full screen" onerror="setUnavailable(<?= $camera['Id'] ?>)"> -->
                                <img loading="lazy" src="/assets/icons/photo-camera.svg" data-src="<?= __SERVER_URL__ ?>/api/stream.mjpeg?src=camera_<?= $camera['Id'] ?>" camera-type="video" camera-id="<?= $camera['Id'] ?>" class="full-screen-camera-btn pointer" title="Click to full screen" onerror="setUnavailable(<?= $camera['Id'] ?>)">

                                <!-- Left and right text / timestamp -->
                                <div class="camera-image-text-left">
                                    <p><b><?= $camera['Text_left'] ?></b></p>

                                    <?php
                                    // Print timestamp on the right if enabled
                                    if ($camera['Timestamp_left'] == 'true') {
                                        echo '<p class="camera-image-timestamp font-size-12"></p>';
                                    } ?>
                                </div>

                                <div class="camera-image-text-right">
                                    <p class="text-right"><b><?= $camera['Text_right'] ?></b></p>

                                    <?php
                                    // Print timestamp on the right if enabled
                                    if ($camera['Timestamp_right'] == 'true') {
                                        echo '<p class="camera-image-timestamp font-size-12"></p>';
                                    } ?>
                                </div>
                            </div>
                            <?php
                        endif ?>
                    </div>

                    <div class="camera-btn-div flex">
                        <div class="flex align-item-center justify-space-between">
                            <div>
                                <p class="label-green"><b><?= $camera['Name'] ?></b></p>
                            </div>
                            <div class="flex column-gap-10">
                                <div class="slide-btn-medium-tr timelapse-camera-btn" title="See timelapse" camera-id="<?= $camera['Id'] ?>">
                                    <img src="/assets/icons/picture.svg" />
                                    <span>Timelapse</span>
                                </div>

                                <div class="slide-btn-medium-tr configure-camera-btn" title="Configure" camera-id="<?= $camera['Id'] ?>">
                                    <img src="/assets/icons/cog.svg" />
                                    <span>Configure</span>
                                </div>
                            </div>
                        </div>

                        <div class="close-full-screen-container margin-bottom-30 hide" camera-id="<?= $camera['Id'] ?>">
                            <img src="/assets/icons/close.svg" class="close-full-screen-btn pointer lowopacity" camera-id="<?= $camera['Id'] ?>" title="Close full screen" />
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
