<!DOCTYPE html>
<?php
define('ROOT', dirname(__FILE__, 2));

require_once(ROOT . '/controllers/Autoloader.php');

\Controllers\Autoloader::load();

$mysettings = new \Controllers\Settings();
$mymotion = new \Controllers\Motion();

/**
 *  Start or stop motion
 */
if (!empty($_GET['motion'])) {
    $mymotion->stopStart($_GET['motion']);
}
?>

<?php include_once('../includes/head.inc.php');

/**
 *  Get global settings
 */
$settings = $mysettings->get();
$printLiveBtn = $settings['Print_live_btn'];
$printMotionStartBtn = $settings['Print_motion_start_btn'];
$printMotionAutostartBtn = $settings['Print_motion_autostart_btn'];
$printMotionAlertBtn = $settings['Print_motion_alert_btn'];
$printMotionStats = $settings['Print_motion_stats'];
$printMotionCaptures = $settings['Print_motion_captures'];
$printMotionConfig = $settings['Print_motion_config'];

/**
 *  Get motion alert status (enabled or disabled)
 */
$alertEnabled = $mymotion->getAlertStatus();

/**
 *  Get autostart and alert settings
 */
$alertConfiguration = $mymotion->getAlertConfiguration();
$motionStatus = $mymotion->getStatus();
$motionAutostartEnabled = $mymotion->getAutostartStatus();
$autostartConfiguration = $mymotion->getAutostartConfiguration();
$autostartDevicePresenceEnabled = $mymotion->getAutostartOnDevicePresenceStatus();
$autostartKnownDevices = $mymotion->getAutostartDevices();
?>

<body>
    <?php include_once('../includes/settings.php'); ?>

    <div id="motionui-status">
        <?php
        /**
         *  Display a warning if motionUI service is not running
         */
        if ($mymotion->getMotionUIServiceStatus() != 'active') {
            echo '<p class="center yellowtext"><img src="resources/icons/warning.png" class="icon" /><b>motionui</b> service is not started. You must start it.</p>';
        } ?>
    </div>

    <div class="container">
        <?php
        if ($printLiveBtn == 'yes') : ?>
            <div class="item">
                <h2>Live</h2>
                <a href="<?= '/live.php' ?>">
                    <button class="btn-square-green"><img src="resources/icons/camera.png" class="icon" /></button>
                </a>
                <span class="block center lowopacity">Visualize</span>
            </div>
            <?php
        endif;

        if ($printMotionStartBtn == 'yes') : ?>
            <div id="motion-start-div" class="item">
                <h2>Motion</h2>

                <?php
                if ($motionStatus != 'active') : ?>
                    <button id="start-motion-btn" class="btn-square-green" title="Start motion service now">
                        <img src="resources/icons/power.png" class="icon" />
                    </button>
                    <span class="block center lowopacity">Start capture</span>
                    
                    <?php
                endif;

                if ($motionStatus == 'active') {
                    echo '<button id="stop-motion-btn" class="btn-square-red" title="Stop motion service"><img src="resources/icons/power.png" class="icon" /></button>';
                    echo '<span class="block center lowopacity">Stop capture</span>';
                } ?>
            </div>
            <?php
        endif;

        /**
         *  Include autostart btn and settings
         */
        if ($printMotionAutostartBtn == 'yes') {
            include_once('../includes/motion-autostart.php');
        }

        /**
         *  Include alert btn and settings
         */
        if ($printMotionAlertBtn == 'yes') {
            include_once('../includes/motion-alert.php');
        } ?>
    </div>

    <div id="how-to-alert-container" class="config-div hide">
        <?php include_once('../includes/how-to-alert.php'); ?>
    </div>

    <hr>

    <?php
    /**
     *  Include motion stats div
     */
    if ($printMotionStats == 'yes') {
        include_once('../includes/motion-stats.php');
    }

    /**
     *  Include motion configuration div
     */
    if ($printMotionConfig == 'yes') {
        include_once('../includes/motion-configuration.php');
    }

    /**
     *  Footer
     */
    include_once('../includes/footer.inc.php'); ?>
</body>
</html>