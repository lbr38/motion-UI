<div id="top-buttons-container">
    <div>
        <img id="print-userspace-btn" src="resources/icons/user.svg" class="pointer lowopacity" title="Show userspace" />
    </div>
    <div>
        <img id="print-settings-btn" src="resources/icons/cog.svg" class="pointer lowopacity" title="Show settings" />
    </div>
</div>

<?php
    include_once(ROOT . '/views/includes/settings.inc.php');
    include_once(ROOT . '/views/includes/userspace.inc.php');
    include_once(ROOT . '/views/includes/motion-configure-alert.inc.php');
    include_once(ROOT . '/views/includes/motion-configure-autostart.inc.php');
?>

<div id="motionui-status">
    <?php
    /**
     *  Display a warning if motionUI service is not running
     */
    if ($mymotion->getMotionUIServiceStatus() != 'active') {
        echo '<p class="center yellowtext"><img src="resources/icons/warning.png" class="icon" /><b>motionui</b> service is not running. Please start it.</p>';
    } ?>
</div>

<?php
/**
 *  Include main buttons
 */
include_once(ROOT . '/views/includes/main-buttons.inc.php');

/**
 *  Include motion events div
 */
if ($printMotionEvents == 'yes') {
    include_once(ROOT . '/views/includes/motion-events.inc.php');
}

/**
 *  Include motion stats div
 */
if ($printMotionStats == 'yes') {
    include_once(ROOT . '/views/includes/motion-stats.inc.php');
}

/**
 *  Include motion configuration div
 */
if ($printMotionConfig == 'yes') {
    include_once(ROOT . '/views/includes/motion-configuration.inc.php');
}

include_once(ROOT . '/views/includes/print-event.inc.php');