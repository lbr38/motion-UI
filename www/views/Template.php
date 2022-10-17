<?php

namespace Views;

class Template
{
    public function __construct(string $page)
    {
        if ($page != 'motion' and $page != 'live') {
            throw new Exception('The page you try to render does not exist.');
        }

        $this->page = $page;
    }

    public function render()
    {
        ob_start();

        if ($this->page == 'motion') {
            $this->renderMotion();
        }

        if ($this->page == 'live') {
            $this->renderLive();
        }

        $content = ob_get_clean();

        include_once('../templates/template.php');
    }

    private function renderMotion()
    {
        $mysettings = new \Controllers\Settings();
        $mymotion = new \Controllers\Motion();

        /**
         *  Get global settings
         */
        $settings = $mysettings->get();
        $printLiveBtn = $settings['Print_live_btn'];
        $printMotionStartBtn = $settings['Print_motion_start_btn'];
        $printMotionAutostartBtn = $settings['Print_motion_autostart_btn'];
        $printMotionAlertBtn = $settings['Print_motion_alert_btn'];
        $printMotionStats = $settings['Print_motion_stats'];
        $printMotionEvents = $settings['Print_motion_events'];
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
        $autostartKnownDevices = $mymotion->getAutostartDevices(); ?>

        <div id="top-buttons-container">
            <div>
                <img id="print-userspace-btn" src="resources/icons/user.svg" class="pointer lowopacity" title="Show userspace" />
            </div>
            <div>
                <img id="print-settings-btn" src="resources/icons/cog.svg" class="pointer lowopacity" title="Show settings" />
            </div>
        </div>

        <?php
            include_once('../includes/settings.inc.php');
            include_once('../includes/userspace.inc.php');
            include_once('../includes/motion-configure-alert.inc.php');
            include_once('../includes/motion-configure-autostart.inc.php');
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

        <?php include_once('../includes/main-buttons.inc.php'); ?>

        <?php
        /**
         *  Include motion events div
         */
        if ($printMotionEvents == 'yes') {
            include_once('../includes/motion-events.inc.php');
        }

        /**
         *  Include motion stats div
         */
        if ($printMotionStats == 'yes') {
            include_once('../includes/motion-stats.inc.php');
        }

        /**
         *  Include motion configuration div
         */
        if ($printMotionConfig == 'yes') {
            include_once('../includes/motion-configuration.inc.php');
        }
    }

    private function renderLive()
    {
        $mycamera = new \Controllers\Camera();

        /**
         *  Get all cameras Id
         */
        $camerasTotal = $mycamera->getTotal(); ?>

        <div id="top-buttons-container">
            <div>
                <a href="/"><img src="resources/icons/back.svg" class="pointer lowopacity" title="Go back" /></a>
            </div>

            <div>
                <img id="print-new-camera-btn" src="resources/icons/plus.svg" class="pointer lowopacity" title="Add a camera" />
            </div>
        </div>

        <?php include_once('../includes/new-camera.inc.php'); ?>

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
            if ($camerasTotal > 0) : ?>
                <?php
                $camerasIds = $mycamera->getCamerasIds();

                foreach ($camerasIds as $camerasId) {
                    $mycamera->display($camerasId);
                }
            endif ?>
        </div>

        <?php
    }
}