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

        if (!is_dir(CAMERAS_DIR)) {
            mkdir(CAMERAS_DIR, 0770, true);

            chgrp(CAMERAS_DIR, 'motion');
            chmod(CAMERAS_DIR, octdec('0770'));
        }
        if (!is_dir(CAPTURES_DIR)) {
            mkdir(CAPTURES_DIR, 0770, true);

            chmod(CAPTURES_DIR, octdec('0770'));
            chgrp(CAPTURES_DIR, 'motion');
        }
        if (!is_dir(DB_UPDATE_DONE_DIR)) {
            mkdir(DB_UPDATE_DONE_DIR, 0770, true);
        }
    }
}
