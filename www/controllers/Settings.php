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
        $printLiveBtn = 'no';
        $printMotionStartBtn = 'no';
        $printMotionAutostartBtn = 'no';
        $printMotionAlertBtn = 'no';
        $printMotionStatsBtn = 'no';
        $printMotionsCaptures = 'no';
        $printMotionConfig = 'no';

        $settings = json_decode($settings, true);

        if (!empty($settings['print-live-btn']) and $settings['print-live-btn'] == 'yes') {
            $printLiveBtn = 'yes';
        }
        if (!empty($settings['print-motion-start-btn']) and $settings['print-motion-start-btn'] == 'yes') {
            $printMotionStartBtn = 'yes';
        }
        if (!empty($settings['print-motion-autostart-btn']) and $settings['print-motion-autostart-btn'] == 'yes') {
            $printMotionAutostartBtn = 'yes';
        }
        if (!empty($settings['print-motion-alert-btn']) and $settings['print-motion-alert-btn'] == 'yes') {
            $printMotionAlertBtn = 'yes';
        }
        if (!empty($settings['print-motion-stats-btn']) and $settings['print-motion-stats-btn'] == 'yes') {
            $printMotionStatsBtn = 'yes';
        }
        if (!empty($settings['print-motion-captures-btn']) and $settings['print-motion-captures-btn'] == 'yes') {
            $printMotionsCaptures = 'yes';
        }
        if (!empty($settings['print-motion-config-btn']) and $settings['print-motion-config-btn'] == 'yes') {
            $printMotionConfig = 'yes';
        }

        $this->model->edit($printLiveBtn, $printMotionStartBtn, $printMotionAutostartBtn, $printMotionAlertBtn, $printMotionStatsBtn, $printMotionsCaptures, $printMotionConfig);
    }
}
