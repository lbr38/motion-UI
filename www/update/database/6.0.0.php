<?php
/**
 *  6.0.0 update
 */
$cameraController = new \Controllers\Camera\Camera();
$cameraConfigController = new \Controllers\Camera\Config();
$go2rtcController = new \Controllers\Go2rtc\Go2rtc();
$motionConfigController = new \Controllers\Motion\Config();
$motionServiceController = new \Controllers\Motion\Service();

/**
 *  Force admin permissions to be able to edit cameras configuration
 */
if (!defined('IS_ADMIN')) {
    define('IS_ADMIN', true);
}
try {
    /**
     *  Create a backup of the cameras table
     */
    try {
        $this->db->exec("CREATE TABLE IF NOT EXISTS cameras_backup AS SELECT * FROM cameras");
    } catch (\Exception $e) {
        throw new Exception('Error while creating backup table in database: ' . $e->getMessage() . PHP_EOL);
    }

    /**
     *  Add webrtc section in go2rtc config file
     */
    try {
        $config = yaml_parse_file(GO2RTC_DIR . '/go2rtc.yml');

        if ($config === false) {
            throw new Exception('Failed to parse go2rtc config file');
        }

        /**
         *  If webrtc key is not present, add it
         */
        if (!isset($config['webrtc'])) {
            $config['webrtc'] = [
                'listen' => ':8555',
                'candidates' => [
                    'stun:8555'
                ]
            ];
        }

        /**
         *  Write new configuration
         */
        if (!yaml_emit_file(GO2RTC_DIR . '/go2rtc.yml', $config)) {
            throw new Exception('Failed to write to file');
        }

        /**
         *  Restart go2rtc
         */
        $go2rtcController->restart();
    } catch (\Exception $e) {
        throw new Exception('Error while updating ' . GO2RTC_DIR . '/go2rtc.yml: ' . $e->getMessage() . PHP_EOL);
    }

    /**
     *  Create temporary cameras table
     */
    try {
        $this->db->exec("CREATE TABLE IF NOT EXISTS cameras_new (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        Configuration TEXT)");
    } catch (\Exception $e) {
        throw new Exception('Error while creating cameras_new table in database: ' . $e->getMessage() . PHP_EOL);
    }

    /**
     *  Migrate cameras configuration to the new table
     *  Get all cameras
     */
    $cameras = $cameraController->get();

    if (!empty($cameras)) {
        foreach ($cameras as $camera) {
            $id = $camera['Id'];

            /**
             *  Get camera configuration
             */
            $currentConfiguration = $cameraController->getConfiguration($id);

            /**
             *  Get camera config template
             */
            $template = $cameraConfigController->getTemplate();

            /**
             *  Update template with current configuration
             */
            $template['name'] = $currentConfiguration['Name'];
            $template['url'] = $currentConfiguration['Url'];
            $template['width'] = explode('x', $currentConfiguration['Output_resolution'])[0];
            $template['height'] = explode('x', $currentConfiguration['Output_resolution'])[1];
            $template['framerate'] = $currentConfiguration['Framerate'];
            if (empty($currentConfiguration['Rotate'])) {
                $currentConfiguration['Rotate'] = 0;
            }
            $template['rotate'] = $currentConfiguration['Rotate'];
            $template['text-left'] = $currentConfiguration['Text_left'];
            $template['text-right'] = $currentConfiguration['Text_right'];
            $template['timestamp-left'] = $currentConfiguration['Timestamp_left'];
            $template['timestamp-right'] = $currentConfiguration['Timestamp_right'];
            $template['basic-auth-username'] = $currentConfiguration['Username'];
            $template['basic-auth-password'] = $currentConfiguration['Password'];
            $template['stream-enable'] = $currentConfiguration['Live_enabled'];
            $template['motion-detection-enable'] = $currentConfiguration['Motion_enabled'];
            $template['timelapse-enable'] = $currentConfiguration['Timelapse_enabled'];
            $template['hardware-acceleration'] = $currentConfiguration['Hardware_acceleration'];

            /**
             *  Encode template to JSON
             */
            try {
                $templateJson = json_encode($template, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new Exception('Error while migrating camera configuration, could not encode configuration to JSON: ' . $e->getMessage() . PHP_EOL);
            }

            /**
             *  Insert new configuration
             */
            try {
                $stmt = $this->db->prepare("INSERT INTO cameras_new (Id, Configuration) VALUES (:id, :configuration)");
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':configuration', $templateJson);
                $stmt->execute();
            } catch (\Exception $e) {
                throw new Exception('Error while migrating camera configuration, could not add camera to database: ' . $e->getMessage() . PHP_EOL);
            }
        }
    }

    /**
     *  Now rename the tables
     */
    try {
        $this->db->exec("DROP TABLE cameras");
        $this->db->exec("CREATE TABLE IF NOT EXISTS cameras (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        Configuration TEXT)");
        $this->db->exec("INSERT INTO cameras SELECT * FROM cameras_new");
        $this->db->exec("DROP TABLE cameras_new");
        $this->db->exec("VACUUM");
    } catch (\Exception $e) {
        throw new Exception('Error while migrating cameras tables in database: ' . $e->getMessage() . PHP_EOL);
    }

    /**
     *  Now, apply cameras params back, this will update go2rtc and motion configuration
     */
    if (!empty($cameras)) {
        foreach ($cameras as $camera) {
            $id = $camera['Id'];

            /**
             *  Get camera configuration
             */
            $currentConfiguration = $cameraController->getConfiguration($id);

            /**
             *  Decode JSON
             */
            try {
                $currentConfiguration = json_decode($currentConfiguration['Configuration'], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new Exception('Error while applying camera configuration, could not decode camera #' . $id . ' configuration from JSON: ' . $e->getMessage() . PHP_EOL);
            }

            $params['name'] = $currentConfiguration['name'];
            $params['url'] = $currentConfiguration['url'];
            $params['resolution'] = $currentConfiguration['width'] . 'x' . $currentConfiguration['height'];
            $params['framerate'] = $currentConfiguration['framerate'];
            $params['rotate'] = $currentConfiguration['rotate'];
            $params['text-left'] = $currentConfiguration['text-left'];
            $params['text-right'] = $currentConfiguration['text-right'];
            $params['timestamp-left'] = $currentConfiguration['timestamp-left'];
            $params['timestamp-right'] = $currentConfiguration['timestamp-right'];
            $params['basic-auth-username'] = $currentConfiguration['basic-auth-username'];
            $params['basic-auth-password'] = $currentConfiguration['basic-auth-password'];
            $params['stream-enable'] = $currentConfiguration['stream-enable'];
            $params['motion-detection-enable'] = $currentConfiguration['motion-detection-enable'];
            $params['timelapse-enable'] = $currentConfiguration['timelapse-enable'];
            $params['hardware-acceleration'] = $currentConfiguration['hardware-acceleration'];

            /**
             *  Apply params
             */
            $cameraController->edit($id, $params);
        }
    }

    /**
     *  Now, remove deprecated motion params
     *  Get every motion config file
     */
    $files = glob(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/*.conf');

    if (!empty($files)) {
        try {
            foreach ($files as $file) {
                /**
                 *  Read each file
                 */
                $params = $motionConfigController->getConfig($file);

                /**
                 *  Rename params
                 */
                if (isset($params['camera_id'])) {
                    $params['device_id'] = $params['camera_id'];
                    unset($params['camera_id']);
                }
                if (isset($params['camera_name'])) {
                    $params['device_name'] = $params['camera_name'];
                    unset($params['camera_name']);
                }

                /**
                 *  Remove some params
                 */
                if (isset($params['movie_codec'])) {
                    unset($params['movie_codec']);
                }
                if (isset($params['movie_bps'])) {
                    unset($params['movie_bps']);
                }
                if (isset($params['target_dir'])) {
                    unset($params['target_dir']);
                }

                /**
                 *  Add new params
                 */
                $params['movie_quality'] = ['status' => 'enabled', 'value' => '60'];
                $params['movie_container'] = ['status' => 'enabled', 'value' => 'mp4'];

                /**
                 *  Save new config file
                 */
                $motionConfigController->write($file, $params);
            }
        } catch (\Exception $e) {
            throw new Exception('Error while migrating motion config file ' . $file . ': ' . $e->getMessage() . PHP_EOL);
        }
    }

    /**
     *  Trigger motion restart
     */
    $motionServiceController->restart();

    unset($cameraController, $go2rtcController, $motionConfigController, $motionServiceController, $files);
} catch (\Exception | \TypeError $e) {
    $error = 'update failed: ' . $e->getMessage() . PHP_EOL;

    /**
     *  Restore cameras table backup
     */
    try {
        $this->db->exec("DROP TABLE cameras");
        $this->db->exec("CREATE TABLE IF NOT EXISTS cameras AS SELECT * FROM cameras_backup");
        $this->db->exec("DROP TABLE cameras_backup");
    } catch (\Exception $e) {
        $error .= 'Error while restoring cameras backup in database: ' . $e->getMessage() . PHP_EOL;
    }

    throw new Exception($error);
}
