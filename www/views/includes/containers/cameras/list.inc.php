<section class="main-container reloadable-container" container="cameras/list">
    <?php
    if ($cameraTotal > 0) :
        if (__ACTUAL_URI__[1] == '') : ?>
            <h3>CAMERAS</h3>
            <?php
        endif;

        if ((__ACTUAL_URI__[1] == 'live') or ((__ACTUAL_URI__[1] == '') and STREAM_ON_MAIN_PAGE === true)) : ?>
            <div id="live-grid-layout-btns">
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
                    $camera = $mycamera->getConfiguration($cameraId);

                    /**
                     *  If camera stream is disabled and we are on live page, skip this camera
                     */
                    if (__ACTUAL_URI__[1] == 'live' or (__ACTUAL_URI__[1] == 'ajax')) {
                        if ($camera['Live_enabled'] == 'false') {
                            continue;
                        }
                    } ?>

                    <div class="camera-container" camera-id="<?= $camera['Id'] ?>">
                        <?php
                        /**
                         *  Display camera only if allowed on this page and if URL starts with http(s)://
                         */
                        if (((__ACTUAL_URI__[1] == 'live') or ((__ACTUAL_URI__[1] == '') and STREAM_ON_MAIN_PAGE === true) or (__ACTUAL_URI__[1] == 'ajax'))) : ?>
                            <div class="camera-output">
                                <?php
                                if ($camera['Live_enabled'] == 'false') : ?>
                                    <div class="flex align-item-center">
                                        <div>
                                            <button class="btn-square-none"><img src="/assets/icons/close.svg" class="icon" title="Live stream is disabled" /></button>
                                            <span class="block center lowopacity-cst">Live stream is disabled</span>
                                        </div>
                                    </div>
                                    <?php
                                endif;

                                if ($camera['Live_enabled'] == 'true') : ?>
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
                                    <div class="camera-image hide" camera-id="<?= $camera['Id'] ?>">
                                        <?php
                                        /**
                                         *  Type 'image'
                                         */
                                        if ($camera['Output_type'] == 'image') : ?>
                                            <img src="/image?id=<?= $camera['Id'] ?>" camera-type="image" camera-id="<?= $camera['Id'] ?>" camera-refresh="<?= $camera['Refresh'] ?>" refresh-timestamp="" style="transform:rotate(<?= $camera['Rotate'] ?>deg);" class="full-screen-camera-btn pointer" title="Click to full screen" onerror="setUnavailable(<?= $camera['Id'] ?>)">
                                            <?php
                                        endif;

                                        /**
                                         *  Type 'video'
                                         */
                                        if ($camera['Output_type'] == 'video') : ?>
                                            <img src="/stream?id=<?= $camera['Id'] ?>" camera-type="video" camera-id="<?= $camera['Id'] ?>" style="transform:rotate(<?= $camera['Rotate'] ?>deg);" class="full-screen-camera-btn pointer" title="Click to full screen" onerror="setUnavailable(<?= $camera['Id'] ?>)">
                                            <?php
                                        endif ?>
                                    </div>
                                    <?php
                                endif ?>
                            </div>
                            <?php
                        endif ?>

                        <div class="camera-btn-div">
                            <div>
                                <p class="label-green"><b><?= $camera['Name'] ?></b></p>
                            </div>
                            <div>
                                <div>
                                    <button class="close-full-screen-camera-btn btn-medium-yellow" camera-id="<?= $camera['Id'] ?>">Close full screen</button>
                                </div>
                                <div class="slide-btn configure-camera-btn" title="Configure" camera-id="<?= $camera['Id'] ?>">
                                    <img src="/assets/icons/cog.svg" />
                                    <span>Configure</span>
                                </div>

                                <div class="slide-btn-red delete-camera-btn" title="Delete" camera-id="<?= $camera['Id'] ?>">
                                    <img src="/assets/icons/delete.svg" />
                                    <span>Delete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                endforeach;
            endif ?>
        </div>
        <?php
    endif; ?>
</section>
