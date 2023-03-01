<?php

namespace Controllers;

use Exception;

class Settings
{
    private $model;

    public function __construct()
    {
        $this->model = new \Models\Settings();
    }

    /**
     *  Return global settings
     */
    public function get()
    {
        return $this->model->get();
    }

    /**
     *  Edit global settings
     */
    public function edit($settings)
    {
        $streamMainPage = 'false';
        $streamLivePage = 'false';
        $motionStartBtn = 'false';
        $motionAutostartBtn = 'false';
        $motionAlertBtn = 'false';
        $motionStats = 'false';
        $motionEvents = 'false';
        $motionEventsVideosThumbnail = 'false';
        $motionEventsPicturesThumbnail = 'false';
        $motionEventsRetention = '30';

        $settings = json_decode($settings, true);

        if (!empty($settings['stream-main-page']) and $settings['stream-main-page'] == 'true') {
            $streamMainPage = 'true';
        }
        if (!empty($settings['stream-live-page']) and $settings['stream-live-page'] == 'true') {
            $streamLivePage = 'true';
        }
        if (!empty($settings['motion-start-btn']) and $settings['motion-start-btn'] == 'true') {
            $motionStartBtn = 'true';
        }
        if (!empty($settings['motion-autostart-btn']) and $settings['motion-autostart-btn'] == 'true') {
            $motionAutostartBtn = 'true';
        }
        if (!empty($settings['motion-alert-btn']) and $settings['motion-alert-btn'] == 'true') {
            $motionAlertBtn = 'true';
        }
        if (!empty($settings['motion-stats']) and $settings['motion-stats'] == 'true') {
            $motionStats = 'true';
        }
        if (!empty($settings['motion-events']) and $settings['motion-events'] == 'true') {
            $motionEvents = 'true';
        }
        if (!empty($settings['motion-events-videos-thumbnail']) and $settings['motion-events-videos-thumbnail'] == 'true') {
            $motionEventsVideosThumbnail = 'true';
        }
        if (!empty($settings['motion-events-pictures-thumbnail']) and $settings['motion-events-pictures-thumbnail'] == 'true') {
            $motionEventsPicturesThumbnail = 'true';
        }
        if (!empty($settings['motion-events-retention']) and is_numeric($settings['motion-events-retention']) and $settings['motion-events-retention'] > 0) {
            $motionEventsRetention = $settings['motion-events-retention'];
        }

        $this->model->edit($streamMainPage, $streamLivePage, $motionStartBtn, $motionAutostartBtn, $motionAlertBtn, $motionEvents, $motionEventsVideosThumbnail, $motionEventsPicturesThumbnail, $motionEventsRetention, $motionStats);
    }

    /**
     *  Enable / disable motion configuration's advanced edition mode
     */
    public function motionAdvancedEditionMode(string $status)
    {
        $this->model->motionAdvancedEditionMode($status);
    }
}
