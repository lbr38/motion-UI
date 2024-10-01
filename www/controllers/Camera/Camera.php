<?php

namespace Controllers\Camera;

use Exception;

class Camera
{
    private $model;
    private $id;
    private $name;
    private $url;
    private $rotate;

    private $motionConfigController;
    private $motionServiceController;
    private $go2rtcController;

    public function __construct()
    {
        $this->model = new \Models\Camera\Camera();
        $this->motionConfigController = new \Controllers\Motion\Config();
        $this->motionServiceController = new \Controllers\Motion\Service();
        $this->go2rtcController = new \Controllers\Go2rtc\Go2rtc();
    }

    /**
     *  Get all cameras
     */
    public function get()
    {
        return $this->model->get();
    }

    /**
     *  Get camera Id by its name
     */
    public function getIdByName(string $name)
    {
        return $this->model->getIdByName($name);
    }

    /**
     *  Get camera name by its Id
     */
    public function getNameById(string $id)
    {
        return $this->model->getNameById($id);
    }

    /**
     *  Get camera name by motion event Id
     */
    public function getNameByEventId(string $motionEventId)
    {
        return $this->model->getNameByEventId($motionEventId);
    }

    /**
     *  Get camera's configuration
     */
    public function getConfiguration(string $id)
    {
        return $this->model->getConfiguration($id);
    }

    /**
     *  Returns the total count of cameras
     */
    public function getTotal()
    {
        /**
         *  Get total camera config files
         */
        return count($this->getCamerasIds());
    }

    /**
     *  Returns all camera Id
     */
    public function getCamerasIds()
    {
        return $this->model->getCamerasIds();
    }

    /**
     *  Add a new camera
     */
    public function add(array $params)
    {
        $motionParams = [];

        /**
         *  Define some default params values
         */
        $basicAuthUsername = '';
        $basicAuthPassword = '';
        $motionEnabled = 'false';

        /**
         *  Get that minimal required parameters are set
         */
        if (empty($params['name'])) {
            throw new Exception('Name is required');
        }
        if (empty($params['url'])) {
            throw new Exception('URL or device is required');
        }
        if (empty($params['resolution'])) {
            throw new Exception('Resolution is required');
        }
        if (!isset($params['framerate'])) {
            throw new Exception('Frame rate is required');
        }

        /**
         *  Retrieve params
         */
        $name = \Controllers\Common::validateData($params['name']);
        $url = \Controllers\Common::validateData($params['url']);
        $resolution = \Controllers\Common::validateData($params['resolution']);
        $framerate = \Controllers\Common::validateData($params['framerate']);

        /**
         *  If Basic auth username and password are set
         */
        if (!empty($params['basic-auth-username'])) {
            $basicAuthUsername = \Controllers\Common::validateData($params['basic-auth-username']);
        }
        if (!empty($params['basic-auth-password'])) {
            $basicAuthPassword = \Controllers\Common::validateData($params['basic-auth-password']);
        }

        /**
         *  If motion detection is enabled
         */
        if (!empty($params['motion-detection-enable']) and $params['motion-detection-enable'] == 'true') {
            $motionEnabled = 'true';
        }

        /**
         *  Check that URL starts with http(s)://, rtsp:// or /dev/video
         */
        if (!preg_match('#(^https?://|^rtsp://|^/dev/video)#', $url)) {
            throw new Exception('URL must start with <b>http(s)://</b>, <b>rtsp://</b> or <b>/dev/video</b>');
        }

        /**
         *  Check that resolution is valid
         */
        if (!preg_match('#^([0-9]+)x([0-9]+)$#', $resolution)) {
            throw new Exception('Specified resolution is invalid');
        }

        /**
         *  Check that frame rate is valid
         */
        if (!is_numeric($framerate) or $framerate < 0) {
            throw new Exception('Frame rate value is invalid');
        }

        /**
         *  Add camera in database
         */
        $this->model->add($name, $url, $resolution, $framerate, $basicAuthUsername, $basicAuthPassword, $motionEnabled);

        /**
         *  Get new camera Id from database
         */
        $id = $this->getIdByName($name);

        if (empty($id)) {
            throw new Exception('Could not retrieve camera Id');
        }

        /**
         *  Prepare motion configuration parameters
         */
        $motionParams['camera_id']   = ['status' => 'enabled', 'value' => $id];
        $motionParams['camera_name'] = ['status' => 'enabled', 'value' => $name];
        $motionParams['width']       = ['status' => 'enabled', 'value' => explode('x', $resolution)[0]];
        $motionParams['height']      = ['status' => 'enabled', 'value' => explode('x', $resolution)[1]];

        /**
         *  Motion configuration
         *  Generate a new motion configuration file for this camera
         */
        $this->motionConfigController->generateCameraConfig($id);

        /**
         *  Edit motion configuration file for this camera
         */
        $this->motionConfigController->edit(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf', $motionParams);

        /**
         *  Enable / disable motion configuration file
         */
        if ($motionEnabled == 'true') {
            $this->motionConfigController->enable($id);
        } else {
            $this->motionConfigController->disable($id);
        }

        /**
         *  Add a new stream in go2rtc
         */
        $params = [
            'id' => $id,
            'url' => $url,
            'basicAuthUsername' => $basicAuthUsername,
            'basicAuthPassword' => $basicAuthPassword,
            'rotate' => 0,
            'resolution' => $resolution,
            'framerate' => $framerate,
            'hardware_acceleration' => 'false'
        ];
        $this->go2rtcController->addStream($id, $params);
    }

    /**
     *  Delete camera
     */
    public function delete(string $id)
    {
        $motionRestart = false;

        /**
         *  Check if camera Id exist
         */
        if (!$this->existId($id)) {
            throw new Exception('Camera does not exist');
        }

        /**
         *  Delete camera in database
         */
        $this->model->delete($id);

        /**
         *  Delete camera config file
         */
        if (file_exists(CAMERAS_MOTION_CONF_ENABLED_DIR . '/camera-' . $id . '.conf')) {
            if (!unlink(CAMERAS_MOTION_CONF_ENABLED_DIR . '/camera-' . $id . '.conf')) {
                throw new Exception('Could not delete camera config file: ' . CAMERAS_MOTION_CONF_ENABLED_DIR . '/camera-' . $id . '.conf');
            }

            // Trigger motion restart if camera config file was enabled
            $motionRestart = true;
        }
        if (file_exists(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf')) {
            if (!unlink(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf')) {
                throw new Exception('Could not delete camera config file: ' . CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf');
            }
        }

        /**
         *  Delete camera data directory (timelapse images)
         */
        if (is_dir(CAMERAS_TIMELAPSE_DIR . '/camera-' . $id)) {
            if (!\Controllers\Filesystem\Directory::deleteRecursive(CAMERAS_TIMELAPSE_DIR . '/camera-' . $id)) {
                throw new Exception('Could not delete camera data directory: ' . CAMERAS_TIMELAPSE_DIR . '/camera-' . $id);
            }
        }

        /**
         *  Restart motion service if running
         */
        if ($this->motionServiceController->isRunning() and $motionRestart) {
            if (!file_exists(DATA_DIR . '/motion.restart')) {
                touch(DATA_DIR . '/motion.restart');
            }
        }

        /**
         *  Remove stream from go2rtc
         */
        $this->go2rtcController->removeStream($id);
    }

    /**
     *  Edit camera global settings
     */
    public function editGlobalSettings(int $id, array $params)
    {
        $motionParams = [];
        $motionRestart = false;

        /**
         *  Define some default params values
         */
        $rotate = 0;
        $textLeft = '';
        $textRight = '';
        $basicAuthUsername = '';
        $basicAuthPassword = '';
        $liveEnabled = 'false';
        $timestampLeft = 'false';
        $timestampRight = 'false';
        $motionEnabled = 'false';
        $timelapseEnabled = 'false';
        $hardwareAcceleration = 'false';

        /**
         *  Check if camera Id exist
         */
        if (!$this->existId($id)) {
            throw new Exception('Camera does not exist');
        }

        /**
         *  Get actual configuration for this camera
         */
        $actualConfiguration = $this->getConfiguration($id);

        /**
         *  Get that minimal required parameters are set
         */
        if (empty($params['name'])) {
            throw new Exception('Name is required');
        }
        if (empty($params['url'])) {
            throw new Exception('URL or device is required');
        }
        if (empty($params['resolution'])) {
            throw new Exception('Resolution is required');
        }
        if (!isset($params['framerate'])) {
            throw new Exception('Frame rate is required');
        }

        /**
         *  Retrieve params
         */
        $name = \Controllers\Common::validateData($params['name']);
        $url = \Controllers\Common::validateData($params['url']);
        $resolution = \Controllers\Common::validateData($params['resolution']);
        $framerate = \Controllers\Common::validateData($params['framerate']);

        /**
         *  If rotate is set
         */
        if (!empty($params['rotate'])) {
            $rotate = \Controllers\Common::validateData($params['rotate']);

            // Check that rotate value is numeric
            if (!is_numeric($rotate)) {
                throw new Exception('Specified rotation is invalid');
            }
        }

        /**
         *  If text left or right is set
         */
        if (!empty($params['text-left'])) {
            $textLeft = \Controllers\Common::validateData($params['text-left']);
        }
        if (!empty($params['text-right'])) {
            $textRight = \Controllers\Common::validateData($params['text-right']);
        }

        /**
         *  If Basic auth username and password are set
         */
        if (!empty($params['basic-auth-username'])) {
            $basicAuthUsername = \Controllers\Common::validateData($params['basic-auth-username']);
        }
        if (!empty($params['basic-auth-password'])) {
            $basicAuthPassword = \Controllers\Common::validateData($params['basic-auth-password']);
        }

        /**
         *  If live stream is enabled
         */
        if (!empty($params['live-enable']) and $params['live-enable'] == 'true') {
            $liveEnabled = 'true';
        }

        /**
         *  If timestamp left or right is enabled
         */
        if (!empty($params['timestamp-left']) and $params['timestamp-left'] == 'true') {
            $timestampLeft = 'true';
        }
        if (!empty($params['timestamp-right']) and $params['timestamp-right'] == 'true') {
            $timestampRight = 'true';
        }

        /**
         *  If hardware acceleration is enabled
         */
        if (!empty($params['hardware-acceleration']) and $params['hardware-acceleration'] == 'true') {
            $hardwareAcceleration = 'true';
        }

        /**
         *  If motion detection is enabled
         */
        if (!empty($params['motion-detection-enable']) and $params['motion-detection-enable'] == 'true') {
            $motionEnabled = 'true';
        }

        /**
         *  If timelapse is enabled
         */
        if (!empty($params['timelapse-enable']) and $params['timelapse-enable'] == 'true') {
            $timelapseEnabled = 'true';
        }

        /**
         *  Check that URL starts with http(s)://, rtsp:// or /dev/video
         */
        if (!preg_match('#(^https?://|^rtsp://|^/dev/video)#', $url)) {
            throw new Exception('URL must start with <b>http(s)://</b>, <b>rtsp://</b> or <b>/dev/video</b>');
        }

        /**
         *  Check that resolution is valid
         */
        if (!preg_match('#^([0-9]+)x([0-9]+)$#', $resolution)) {
            throw new Exception('Specified resolution is invalid');
        }

        /**
         *  Check that frame rate is valid
         */
        if (!is_numeric($framerate) or $framerate < 0) {
            throw new Exception('Frame rate value is invalid');
        }

        /**
         *  Check if config file exist and is readable and writeable
         */
        if (!file_exists(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf')) {
            throw new Exception('Cannot found camera configuration file');
        }
        if (!is_readable(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf')) {
            throw new Exception('Camera configuration file is not readable');
        }
        if (!is_writeable(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf')) {
            throw new Exception('Camera configuration file is not writeable');
        }

        /**
         *  Check if some params have changed compared to the actual configuration in database
         *  If so, then motion service will be restarted
         *  Params that will trigger a motion service restart:
         *    - resolution
         *    - text left
         *    - text right
         */
        if ($actualConfiguration['Output_resolution'] != $resolution) {
            $motionRestart = true;
        }
        if ($actualConfiguration['Text_left'] != $textLeft) {
            $motionRestart = true;
        }
        if ($actualConfiguration['Text_right'] != $textRight) {
            $motionRestart = true;
        }

        /**
         *  Edit global settings in database
         */
        $this->model->editGlobalSettings($id, $name, $url, $resolution, $framerate, $rotate, $textLeft, $textRight, $basicAuthUsername, $basicAuthPassword, $liveEnabled, $timestampLeft, $timestampRight, $motionEnabled, $timelapseEnabled, $hardwareAcceleration);

        /**
         *  Prepare motion configuration parameters
         */
        $motionParams['camera_id']   = ['status' => 'enabled', 'value' => $id];
        $motionParams['camera_name'] = ['status' => 'enabled', 'value' => $name];
        $motionParams['width']       = ['status' => 'enabled', 'value' => explode('x', $resolution)[0]];
        $motionParams['height']      = ['status' => 'enabled', 'value' => explode('x', $resolution)[1]];
        $motionParams['text_left']   = ['status' => 'enabled', 'value' => $textLeft];
        $motionParams['text_right']  = ['status' => 'enabled', 'value' => $textRight];

        /**
         *  Edit camera motion configuration file
         */
        $this->motionConfigController->edit(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf', $motionParams);

        /**
         *  Enable / disable motion configuration file
         */
        if ($motionEnabled == 'true') {
            // Also force motion restart if a change was made in the camera configuration and if $motionEnabled is true
            $this->motionConfigController->enable($id, $motionRestart);
        } else {
            $this->motionConfigController->disable($id);
        }

        /**
         *  Update go2rtc configuration for this stream
         */
        $params = [
            'id' => $id,
            'url' => $url,
            'basicAuthUsername' => $basicAuthUsername,
            'basicAuthPassword' => $basicAuthPassword,
            'rotate' => $rotate,
            'resolution' => $resolution,
            'framerate' => $framerate,
            'hardware_acceleration' => $hardwareAcceleration
        ];
        $this->go2rtcController->editStream($id, $params);
    }

    /**
     *  Check if camera Id exist
     */
    public function existId(string $id)
    {
        return $this->model->existId($id);
    }
}
