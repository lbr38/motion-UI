<div id="top-buttons-container">
    <div id="live-grid-layout-btns">
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
    <div>
        <a href="/"><img src="resources/icons/back.svg" class="pointer lowopacity" title="Go back" /></a>
    </div>
    <div>
        <img id="print-new-camera-btn" src="resources/icons/plus.svg" class="pointer lowopacity" title="Add a camera" />
    </div>
</div>

<?php include_once(ROOT . '/views/includes/new-camera.inc.php'); ?>

<div id="camera-container">
    <?php
    if ($camerasTotal == 0) : ?>
        <div>
            <h2 class="center">GETTING STARTED</h2>
            <p class="center">Use the <img src="resources/icons/plus.svg" class="icon" /> button in the right corner to add a new camera</p> 
        </div>
        <?php
    endif;

    /**
     *  Print cameras if there are
     */
    if ($camerasTotal > 0) :
        $camerasIds = $mycamera->getCamerasIds();

        foreach ($camerasIds as $cameraId) :
            /**
             *  Get camera configuration
             */
            $mycamera->getConfiguration($cameraId);

            include(ROOT . '/views/includes/configure-camera.inc.php'); ?>

            <div class="camera-container" camera-id="<?= $mycamera->getId() ?>" >
                <!-- Loading image -->
                <div class="camera-loading">
                    <button class="btn-square-none"><img src="resources/icons/loading.gif" class="icon" title="Loading image" /></button>
                    <span class="block center lowopacity">Loading image</span>
                </div>

                <!-- Camera image -->
                <div class="camera-image" camera-id="<?= $mycamera->getId() ?>">
                    <img src="/resources/.live/camera<?= $mycamera->getId() ?>/image.jpg" style="transform:rotate(<?= $mycamera->getRotate() ?>deg);" class="full-screen-camera-btn pointer" title="Click to full screen" camera-id="<?= $mycamera->getId() ?>">
                </div>

                <!-- Unavailable image div -->
                <div class="camera-unavailable hide" camera-id="<?= $mycamera->getId() ?>">
                    <button class="btn-square-red"><img src="resources/icons/error-close.svg" class="icon" title="Unavailable" /></button>
                    <span class="block center lowopacity">Unavailable</span>
                </div>

                <br>
                <div class="camera-btn-div">
                    <div>
                        <p><b><?= $mycamera->getName() ?></b></p>
                    </div>
                    <div>
                        <div class="slide-btn configure-camera-btn" title="Configure" camera-id="<?= $mycamera->getId() ?>">
                            <img src="resources/icons/cog.svg" />
                            <span>Configure</span>
                        </div>

                        <div class="slide-btn-red delete-camera-btn" title="Delete" camera-id="<?= $mycamera->getId() ?>">
                            <img src="resources/icons/bin.svg" />
                            <span>Delete</span>
                        </div>
                    </div>
                </div>

                <br>
                <button class="close-full-screen-camera-btn btn-medium-yellow" camera-id="<?= $mycamera->getId() ?>">Close full screen</button>
                <br>

                <script>
                    $(document).ready(function(){
                        /**
                         *  Print loading image for 1sec and load image
                         */
                        setTimeout(function(){
                            reloadImage(<?= $mycamera->getId() ?>);
                        }, 1000);
                    });

                    /**
                     *  Auto reloading images
                     */
                    <?php
                    if (!empty($mycamera->getRefresh())) :
                        $refreshTotal = $mycamera->getRefresh() * 1000; ?>
                        $(document).ready(function(){
                            setInterval(function(){

                                /**
                                 *  Ajax call to get a new image from http camera
                                 */
                                reloadImage(<?= $mycamera->getId() ?>);
                            }, <?= $refreshTotal ?>);
                        });
                        <?php
                    endif ?>
                </script>
            </div>
            <?php
        endforeach;
    endif ?>
</div>