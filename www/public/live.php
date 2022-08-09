<!DOCTYPE html>
<?php
define('ROOT', dirname(__FILE__, 2));

require_once(ROOT . '/controllers/Autoloader.php');

\Controllers\Autoloader::load();

$mycamera = new \Controllers\Camera();

/**
 *  Récupération de tous les Ids de camera
 */
$camerasTotal = $mycamera->getTotal();
?>

<?php include_once('../includes/head.inc.php'); ?>

<body>
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
        } ?>

        <div class="item">

            <h2>Add a new camera</h2>

            <span class="block center lowopacity">Add a MJPEG-stream based camera (mjpg_streamer, ustreamer...)</span>
            <br>

            <form id="new-camera-form" autocomplete="off">
                <p>Name:</p>
                <input type="text" name="camera-name" />
                <p>URL:<img src="resources/icons/info.png" class="icon-lowopacity" title="Insert an URL that points directly to a JPEG image." /></p>
                <input type="text" name="camera-url" placeholder="e.g. http(s)://.../snapshot" />
                <br><br>
                <button class="btn-medium-green">Add camera</button>
            </form>
        </div>

        <div class="item">

            <h2>Motion</h2>

            <a href="index.php"><button class="btn-square-green"><img src="resources/icons/motion.png" class="icon"></button></a>
            <span class="block center lowopacity">Configure</span>
        </div>
    </div>

    <?php include_once('../includes/footer.inc.php'); ?>
</body>
</html>