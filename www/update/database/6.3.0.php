<?php
/**
 *  6.3.0 update
 */
$cameraController = new \Controllers\Camera\Camera();
$motionConfigController = new \Controllers\Motion\Config();
$motionTemplateController = new \Controllers\Motion\Template();

/**
 *  Add 'Motion_configuration' column to cameras table
 */
if (!$this->db->columnExist('cameras', 'Motion_configuration')) {
    $this->db->exec("ALTER TABLE cameras ADD COLUMN Motion_configuration TEXT");
}

/**
 *  Remove old columns from cameras table
 */

/**
 *  Remove 'Name' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Name')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Name");
}

/**
 *  Remove 'Url' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Url')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Url");
}

/**
 *  Remove 'Output_resolution' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Output_resolution')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Output_resolution");
}

/**
 *  Remove 'Framerate' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Framerate')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Framerate");
}

/**
 *  Remove 'Rotate' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Rotate')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Rotate");
}

/**
 *  Remove 'Text_left' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Text_left')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Text_left");
}

/**
 *  Remove 'Text_right' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Text_right')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Text_right");
}

/**
 *  Remove 'Timestamp_left' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Timestamp_left')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Timestamp_left");
}

/**
 *  Remove 'Timestamp_right' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Timestamp_right')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Timestamp_right");
}

/**
 *  Remove 'Username' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Username')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Username");
}

/**
 *  Remove 'Password' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Password')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Password");
}

/**
 *  Remove 'Live_enabled' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Live_enabled')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Live_enabled");
}

/**
 *  Remove 'Motion_enabled' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Motion_enabled')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Motion_enabled");
}

/**
 *  Remove 'Timelapse_enabled' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Timelapse_enabled')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Timelapse_enabled");
}

/**
 *  Remove 'Hardware_acceleration' column from cameras table
 */
if ($this->db->columnExist('cameras', 'Hardware_acceleration')) {
    $this->db->exec("ALTER TABLE cameras DROP COLUMN Hardware_acceleration");
}

/**
 *  Vacuum
 */
$this->db->exec("VACUUM");

/**
 *  Migrate motion configuration in database for each camera
 */
try {
    $camerasId = $cameraController->getCamerasIds();

    if (empty($camerasId)) {
        return;
    }
} catch (Exception $e) {
    throw new Exception('could not retrieve cameras Ids: ' . $e->getMessage());
}

/**
 *  Global configuration changes
 */
try {
    /**
     *  Loop through each camera
     */
    foreach ($camerasId as $id) {
        /**
         *  Get current global configuration
         */
        $configuration = $cameraController->getConfiguration($id);

        try {
            $currentGlobalConfig = json_decode($configuration['Configuration'], true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new Exception('error decoding JSON: ' . $e->getMessage());
        }

        /**
         *  Modify global configuration
         */
        // Main stream
        $currentGlobalConfig['main-stream']['device']          = $currentGlobalConfig['url'];
        $currentGlobalConfig['main-stream']['width']           = $currentGlobalConfig['width'];
        $currentGlobalConfig['main-stream']['height']          = $currentGlobalConfig['height'];
        $currentGlobalConfig['main-stream']['resolution']      = $currentGlobalConfig['width'] . 'x' . $currentGlobalConfig['height'];
        $currentGlobalConfig['main-stream']['framerate']       = $currentGlobalConfig['framerate'];
        $currentGlobalConfig['main-stream']['rotate']          = $currentGlobalConfig['rotate'];
        $currentGlobalConfig['main-stream']['text-left']       = $currentGlobalConfig['text-left'];
        $currentGlobalConfig['main-stream']['text-right']      = $currentGlobalConfig['text-right'];
        $currentGlobalConfig['main-stream']['timestamp-left']  = $currentGlobalConfig['timestamp-left'];
        $currentGlobalConfig['main-stream']['timestamp-right'] = $currentGlobalConfig['timestamp-right'];
        // Secondary stream
        $currentGlobalConfig['secondary-stream']['device']     = '';
        $currentGlobalConfig['secondary-stream']['width']      = '640';
        $currentGlobalConfig['secondary-stream']['height']     = '360';
        $currentGlobalConfig['secondary-stream']['resolution'] = '640x360';
        $currentGlobalConfig['secondary-stream']['framerate']  = 25;
        // Authentication
        $currentGlobalConfig['authentication']['username']     = $currentGlobalConfig['basic-auth-username'];
        $currentGlobalConfig['authentication']['password']     = $currentGlobalConfig['basic-auth-password'];
        // Stream
        $currentGlobalConfig['stream']['enable']               = $currentGlobalConfig['stream-enable'];
        $currentGlobalConfig['stream']['technology']           = 'mse';
        // Motion detection
        $currentGlobalConfig['motion-detection']['enable']     = $currentGlobalConfig['motion-detection-enable'];
        // Timelapse
        $currentGlobalConfig['timelapse']['enable']            = $currentGlobalConfig['timelapse-enable'];

        /**
         *  Remove old keys
         */
        unset($currentGlobalConfig['url']);
        unset($currentGlobalConfig['width']);
        unset($currentGlobalConfig['height']);
        unset($currentGlobalConfig['framerate']);
        unset($currentGlobalConfig['rotate']);
        unset($currentGlobalConfig['text-left']);
        unset($currentGlobalConfig['text-right']);
        unset($currentGlobalConfig['timestamp-left']);
        unset($currentGlobalConfig['timestamp-right']);
        unset($currentGlobalConfig['basic-auth-username']);
        unset($currentGlobalConfig['basic-auth-password']);
        unset($currentGlobalConfig['stream-enable']);
        unset($currentGlobalConfig['motion-detection-enable']);
        unset($currentGlobalConfig['timelapse-enable']);

        /**
         *  Encode global configuration to JSON
         */
        try {
            $globalConfigurationJson = json_encode($currentGlobalConfig, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new Exception('error encoding global configuration to JSON: ' . $e->getMessage());
        }

        /**
         *  Write global configuration to database
         */
        $this->db->exec("UPDATE cameras SET Configuration = '" . $globalConfigurationJson . "' WHERE Id = " . $id);
    }
} catch (Exception $e) {
    throw new Exception('could not migrate global configuration: ' . $e->getMessage());
}

/**
 *  Motion configuration migration
 */
try {
    /**
     *  Loop through each camera
     */
    foreach ($camerasId as $id) {
        /**
         *  Get current motion configuration
         */
        $currentMotionConfig = $motionConfigController->getConfig(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf');

        /**
         *  Get motion params template
         */
        $motionParams = $motionTemplateController->get($id);

        /**
         *  Replace values in motion params template with current motion configuration values
         */
        foreach ($currentMotionConfig as $key => $params) {
            $enabled = false;
            $value = '';

            if (isset($params['status']) and $params['status'] === 'enabled') {
                $enabled = true;
            }

            if (isset($params['value'])) {
                $value = $params['value'];
            }

            $motionParams[$key]['value'] = $value;
            $motionParams[$key]['enabled'] = $enabled;
        }

        /**
         *  Encode motion configuration to JSON
         */
        try {
            $motionConfigurationJson = json_encode($motionParams, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new Exception('error encoding motion configuration to JSON: ' . $e->getMessage());
        }

        /**
         *  Write motion configuration to database
         */
        $this->db->exec("UPDATE cameras SET Motion_configuration = '" . $motionConfigurationJson . "' WHERE Id = " . $id);
    }
} catch (Exception $e) {
    throw new Exception('could not migrate motion configuration: ' . $e->getMessage());
}
