<?php
/**
 *  5.0.0 database update
 */
$mycameraController = new \Controllers\Camera\Camera();
$mymotionConfigController = new \Controllers\Motion\Config();
$mymotionServiceController = new \Controllers\Motion\Service();
$mygo2rtcController = new \Controllers\Go2rtc\Go2rtc();

/**
 *  Add 'Framerate' column to cameras table
 */
if (!$this->db->columnExist('cameras', 'Framerate')) {
    $this->db->exec("ALTER TABLE cameras ADD COLUMN Framerate INTEGER DEFAULT 0");
}

/**
 *  Add 'Hardware_acceleration' column to cameras table
 */
if (!$this->db->columnExist('cameras', 'Hardware_acceleration')) {
    $this->db->exec("ALTER TABLE cameras ADD COLUMN Hardware_acceleration CHAR(5) DEFAULT 'false'");
}

/**
 *  Retrieve all cameras
 */
$cameras = $mycameraController->get();

/**
 *  Migrate cameras motion configuration files to new location
 *  Also migrate timelapse directory if exists
 */
if (!empty($cameras)) {
    foreach ($cameras as $camera) {
        $id = $camera['Id'];
        $disabled = false;

        // Default config file location
        $file = CAMERAS_DIR . '/camera-' . $id . '.conf';

        // If a disabled file exists, then use it instead
        if (file_exists(CAMERAS_DIR . '/camera-' . $id . '.conf.disabled')) {
            $file = CAMERAS_DIR . '/camera-' . $id . '.conf.disabled';
            $disabled = true;
        }

        // Move to the available config directory
        if (!rename($file, CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf')) {
            throw new \Exception('Failed to move camera-' . $id . '.conf to ' . CAMERAS_MOTION_CONF_AVAILABLE_DIR);
        }

        // Enable the camera if it was enabled
        if ($disabled === false) {
            if (!symlink(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf', CAMERAS_MOTION_CONF_ENABLED_DIR . '/camera-' . $id . '.conf')) {
                throw new \Exception('Failed to enable camera #' . $id . ' configuration');
            }
        }

        // Move timelapse directory if exists
        if (is_dir(CAMERAS_DIR . '/camera-' . $id . '/timelapse')) {
            if (!rename(CAMERAS_DIR . '/camera-' . $id . '/timelapse', CAMERAS_TIMELAPSE_DIR . '/camera-' . $id)) {
                throw new \Exception('Failed to move timelapse directory for camera #' . $id);
            }
        }
    }
}

/**
 *  Create streams for go2rtc and update Url in motion configuration files
 */
if (!empty($cameras)) {
    foreach ($cameras as $camera) {
        /**
         *  Ignore if Id is empty
         */
        if (empty($camera['Id'])) {
            continue;
        }

        /**
         *  If a Stream_url is set, use it, otherwise use the Url
         */
        if (!empty($camera['Stream_url'])) {
            $url = $camera['Stream_url'];

            /**
             *  Update Url with Stream_url as Stream_url will be abandoned
             */
            $this->db->exec("UPDATE cameras SET Url = '$url' WHERE Id = '{$camera['Id']}'");
        } else {
            $url = $camera['Url'];
        }

        /**
         *  Prepare each params and add stream to go2rtc
         */
        $params = [
            'id' => $camera['Id'],
            'url' => $url,
            'basicAuthUsername' => $camera['Username'],
            'basicAuthPassword' => $camera['Password'],
            'rotate' => $camera['Rotate'],
            'resolution' => $camera['Output_resolution'],
            'framerate' => 0,
            'hardware_acceleration' => 'false',
        ];
        $mygo2rtcController->addStream($camera['Id'], $params);

        /**
         *  Update motion configuration file with new stream url
         */
        $config = [
            'netcam_url' => ['status' => 'enabled', 'value' => 'http://127.0.0.1:1984/api/stream.mjpeg?src=camera_' . $camera['Id']],
            'netcam_high_url' => ['status' => 'disabled', 'value' => ''],
            'netcam_params' => ['status' => 'enabled', 'value' => 'keepalive=on, tolerant_check=on'],
            'rotate' => ['status' => 'disabled', 'value' => ''],
        ];
        $mymotionConfigController->edit(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $camera['Id'] . '.conf', $config);
    }
}

/**
 *  Drop Motion_advanced_edition_mode column from settings table
 */
if ($this->db->columnExist('settings', 'Motion_advanced_edition_mode') === true) {
    $this->db->exec("ALTER TABLE settings DROP COLUMN Motion_advanced_edition_mode");
    $this->db->exec("VACUUM");
}

/**
 *  Drop 'Motion_events_videos_thumbnail' column from settings table
 */
if ($this->db->columnExist('settings', 'Motion_events_videos_thumbnail') === true) {
    $this->db->exec("ALTER TABLE settings DROP COLUMN Motion_events_videos_thumbnail");
    $this->db->exec("VACUUM");
}

/**
 *  Drop 'Motion_events_pictures_thumbnail' column from settings table
 */
if ($this->db->columnExist('settings', 'Motion_events_pictures_thumbnail') === true) {
    $this->db->exec("ALTER TABLE settings DROP COLUMN Motion_events_pictures_thumbnail");
    $this->db->exec("VACUUM");
}

/**
 *  Drop Refresh column from cameras table
 */
if ($this->db->columnExist('cameras', 'Refresh') === true) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Refresh");
    $this->db->exec("VACUUM");
}

/**
 *  Drop Stream_url column from cameras table
 */
if ($this->db->columnExist('cameras', 'Stream_url') === true) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Stream_url");
    $this->db->exec("VACUUM");
}

unset($mycameraController, $mygo2rtcController);
