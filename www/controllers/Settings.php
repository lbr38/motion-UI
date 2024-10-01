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
        $timelapseInterval = '300'; // Default timelapse interval is 300 seconds
        $timelapseRetention = '30'; // Default timelapse retention is 30 days
        $motionEventsRetention = '30';

        /**
         *  Convert JSON to array
         */
        $settings = json_decode($settings, true);

        /**
         *  Check if home page settings is valid
         */
        if (isset($settings['home-page']) and in_array($settings['home-page'], ['live', 'motion', 'events', 'stats'])) {
            $homePage = $settings['home-page'];
        }

        /**
         *  Home page settings is the only one which is not stored in database but stored as a file
         */
        if (!file_put_contents(DATA_DIR . '/.homepage', $homePage)) {
            throw new Exception('Unable to save Home page settings');
        }

        if (!empty($settings['timelapse-interval']) and is_numeric($settings['timelapse-interval']) and $settings['timelapse-interval'] > 0) {
            $timelapseInterval = $settings['timelapse-interval'];
        }
        if (!empty($settings['timelapse-retention']) and is_numeric($settings['timelapse-retention']) and $settings['timelapse-retention'] > 0) {
            $timelapseRetention = $settings['timelapse-retention'];
        }
        if (!empty($settings['motion-events-retention']) and is_numeric($settings['motion-events-retention']) and $settings['motion-events-retention'] > 0) {
            $motionEventsRetention = $settings['motion-events-retention'];
        }

        $this->model->edit($timelapseInterval, $timelapseRetention, $motionEventsRetention);
    }
}
