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
        $homePage = 'live'; // Default home page is '/live'
        $motionEventsVideosThumbnail = 'false';
        $motionEventsPicturesThumbnail = 'false';
        $motionEventsRetention = '30';

        /**
         *  Convert JSON to array
         */
        $settings = json_decode($settings, true);


        if (isset($settings['home-page']) and in_array($settings['home-page'], ['live', 'motion', 'events', 'stats'])) {
            $homePage = $settings['home-page'];
        }

        /**
         *  Home page settings is the only one which is not stored in database but stored as a file
         */
        if (!file_put_contents(DATA_DIR . '/.homepage', $homePage)) {
            throw new Exception('Unable to save Home page settings');
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

        $this->model->edit($motionEventsVideosThumbnail, $motionEventsPicturesThumbnail, $motionEventsRetention);
    }

    /**
     *  Enable / disable motion configuration's advanced edition mode
     */
    public function motionAdvancedEditionMode(string $status)
    {
        $this->model->motionAdvancedEditionMode($status);
    }
}
