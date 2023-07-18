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

        /**
         *  General display settings
         */
        if (!defined('STREAM_ON_MAIN_PAGE')) {
            if (!empty($settings['Stream_on_main_page']) and $settings['Stream_on_main_page'] == 'true') {
                define('STREAM_ON_MAIN_PAGE', true);
            } else {
                define('STREAM_ON_MAIN_PAGE', false);
            }
        }

        if (!defined('STREAM_ON_LIVE_PAGE')) {
            if (!empty($settings['Stream_on_live_page']) and $settings['Stream_on_live_page'] == 'true') {
                define('STREAM_ON_LIVE_PAGE', true);
            } else {
                define('STREAM_ON_LIVE_PAGE', false);
            }
        }

        if (!defined('MOTION_START_BTN')) {
            if (!empty($settings['Motion_start_btn']) and $settings['Motion_start_btn'] == 'true') {
                define('MOTION_START_BTN', true);
            } else {
                define('MOTION_START_BTN', false);
            }
        }

        if (!defined('MOTION_AUTOSTART_BTN')) {
            if (!empty($settings['Motion_autostart_btn']) and $settings['Motion_autostart_btn'] == 'true') {
                define('MOTION_AUTOSTART_BTN', true);
            } else {
                define('MOTION_AUTOSTART_BTN', false);
            }
        }

        if (!defined('MOTION_ALERT_BTN')) {
            if (!empty($settings['Motion_alert_btn']) and $settings['Motion_alert_btn'] == 'true') {
                define('MOTION_ALERT_BTN', true);
            } else {
                define('MOTION_ALERT_BTN', false);
            }
        }

        if (!defined('MOTION_EVENTS')) {
            if (!empty($settings['Motion_events']) and $settings['Motion_events'] == 'true') {
                define('MOTION_EVENTS', true);
            } else {
                define('MOTION_EVENTS', false);
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

        if (!defined('MOTION_STATS')) {
            if (!empty($settings['Motion_stats']) and $settings['Motion_stats'] == 'true') {
                define('MOTION_STATS', true);
            } else {
                define('MOTION_STATS', false);
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
