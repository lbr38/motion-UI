<?php

namespace Controllers\App\Structure;

class Directory
{
    /**
     *  Create app directories if not exist
     */
    public static function create()
    {
        if (!is_dir(DB_DIR)) {
            mkdir(DB_DIR, 0770, true);
        }

        if (!is_dir(LOGS_DIR)) {
            mkdir(LOGS_DIR, 0770, true);
        }

        if (!is_dir(WS_LOGS_DIR)) {
            mkdir(WS_LOGS_DIR, 0770, true);
        }

        if (!is_dir(CAMERAS_DIR)) {
            mkdir(CAMERAS_DIR, 0770, true);

            chgrp(CAMERAS_DIR, 'motionui');
            chmod(CAMERAS_DIR, octdec('0770'));
        }

        if (!is_dir(CAMERAS_MOTION_CONF_AVAILABLE_DIR)) {
            mkdir(CAMERAS_MOTION_CONF_AVAILABLE_DIR, 0770, true);
        }

        if (!is_dir(CAMERAS_MOTION_CONF_ENABLED_DIR)) {
            mkdir(CAMERAS_MOTION_CONF_ENABLED_DIR, 0770, true);
        }

        if (!is_dir(CAMERAS_TIMELAPSE_DIR)) {
            mkdir(CAMERAS_TIMELAPSE_DIR, 0770, true);
        }

        if (!is_dir(CAPTURES_DIR)) {
            mkdir(CAPTURES_DIR, 0770, true);

            chmod(CAPTURES_DIR, octdec('0770'));
            chgrp(CAPTURES_DIR, 'motionui');
        }

        if (!is_dir(GO2RTC_DIR)) {
            mkdir(GO2RTC_DIR, 0770, true);
        }

        if (!is_dir(DB_UPDATE_DONE_DIR)) {
            mkdir(DB_UPDATE_DONE_DIR, 0770, true);
        }
    }
}
