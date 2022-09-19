<!DOCTYPE html>
<?php
define('ROOT', dirname(__FILE__, 2));

require_once(ROOT . '/controllers/Autoloader.php');

\Controllers\Autoloader::load();

$mycamera = new \Controllers\Camera();

/**
 *  Get all cameras Id
 */
$camerasTotal = $mycamera->getTotal();

include_once('../includes/head.inc.php'); ?>

<body>
    <div id="top-buttons-container">
        <div>
            <a href="index.php"><img src="resources/icons/back.svg" class="pointer lowopacity" title="Go back" /></a>
        </div>

        <div>
            <img id="print-new-camera-btn" src="resources/icons/plus.svg" class="pointer lowopacity" title="Add a camera" />
        </div>
    </div>

    <?php include_once('../includes/new-camera.php'); ?>

    <div id="camera-container">
        <?php
        /**
         *  Print cameras if there are
         */
        if ($camerasTotal > 0) {
            $camerasIds = $mycamera->getCamerasIds();

            foreach ($camerasIds as $camerasId) {
                $mycamera->display($camerasId);
            }
        }

        if ($camerasTotal == 0) : ?>
            <div class="item">
                <h2>Getting started</h2>

                <p>Use the <img src="resources/icons/plus.svg" class="icon" /> button in the right corner to add a new camera</p> 
            </div>
            <?php
        endif ?>
    </div>

    <?php include_once('../includes/footer.inc.php'); ?>
</body>
</html>