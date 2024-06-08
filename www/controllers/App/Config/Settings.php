<?php

namespace Controllers\App\Config;

class Settings
{
    public static function get()
    {
        $__LOAD_SETTINGS_ERROR = 0;
        $__LOAD_SETTINGS_MESSAGES = array();

        $mysettings = new \Controllers\Settings();
        $mymotionAlert = new \Controllers\Motion\Alert();

        /**
         *  Get all settings
         */
        $settings = $mysettings->get();

        if (!defined('WWW_HOSTNAME')) {
            if (file_exists(ROOT . '/.fqdn')) {
                define('WWW_HOSTNAME', trim(file_get_contents(ROOT . '/.fqdn')));
            } else {
                define('WWW_HOSTNAME', 'localhost');
            }
        }

        if (!defined('HOME_PAGE')) {
            if (file_exists(DATA_DIR . '/.homepage')) {
                define('HOME_PAGE', trim(file_get_contents(DATA_DIR . '/.homepage')));
            } else {
                define('HOME_PAGE', '');
            }
        }

        if (!defined('TIMELAPSE_INTERVAL')) {
            if (!empty($settings['Timelapse_interval'])) {
                define('TIMELAPSE_INTERVAL', $settings['Timelapse_interval']);
            } else {
                define('TIMELAPSE_INTERVAL', '300');
            }
        }

        if (!defined('TIMELAPSE_RETENTION')) {
            if (!empty($settings['Timelapse_retention'])) {
                define('TIMELAPSE_RETENTION', $settings['Timelapse_retention']);
            } else {
                define('TIMELAPSE_RETENTION', '30');
            }
        }

        if (!defined('MOTION_EVENTS_VIDEOS_THUMBNAIL')) {
            if (!empty($settings['Motion_events_videos_thumbnail']) and $settings['Motion_events_videos_thumbnail'] == 'true') {
                define('MOTION_EVENTS_VIDEOS_THUMBNAIL', true);
            } else {
                define('MOTION_EVENTS_VIDEOS_THUMBNAIL', false);
            }
        }

        if (!defined('MOTION_EVENTS_PICTURES_THUMBNAIL')) {
            if (!empty($settings['Motion_events_pictures_thumbnail']) and $settings['Motion_events_pictures_thumbnail'] == 'true') {
                define('MOTION_EVENTS_PICTURES_THUMBNAIL', true);
            } else {
                define('MOTION_EVENTS_PICTURES_THUMBNAIL', false);
            }
        }

        if (!defined('MOTION_ADVANCED_EDITION_MODE')) {
            if (!empty($settings['Motion_advanced_edition_mode']) and $settings['Motion_advanced_edition_mode'] == 'true') {
                define('MOTION_ADVANCED_EDITION_MODE', true);
            } else {
                define('MOTION_ADVANCED_EDITION_MODE', false);
            }
        }

        if (!defined('MOTION_EVENTS_RETENTION')) {
            if (!empty($settings['Motion_events_retention']) and is_numeric($settings['Motion_events_retention'])) {
                define('MOTION_EVENTS_RETENTION', $settings['Motion_events_retention']);
            } else {
                define('MOTION_EVENTS_RETENTION', '30');
            }
        }

        /**
         *  Alerts settings
         */
        $alertEnabled = $mymotionAlert->getStatus();
        if (!defined('ALERT_ENABLED')) {
            define('ALERT_ENABLED', $alertEnabled);
        }

        if (!defined('__LOAD_SETTINGS_ERROR')) {
            define('__LOAD_SETTINGS_ERROR', $__LOAD_SETTINGS_ERROR);
        }
        if (!defined('__LOAD_SETTINGS_MESSAGES')) {
            define('__LOAD_SETTINGS_MESSAGES', $__LOAD_SETTINGS_MESSAGES);
        }
    }
}
