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

    private $cameraConfigController;
    private $motionConfigController;
    private $motionServiceController;
    private $go2rtcController;

    public function __construct()
    {
        $this->model = new \Models\Camera\Camera();
        $this->cameraConfigController = new \Controllers\Camera\Config();
        $this->motionConfigController = new \Controllers\Motion\Config();
        $this->motionServiceController = new \Controllers\Motion\Service();
        $this->go2rtcController = new \Controllers\Go2rtc\Go2rtc();
    }

    /**
     *  Get all cameras
     */
    public function get() : array
    {
        return $this->model->get();
    }

    /**
     *  Get camera name by its Id
     */
    public function getNameById(string $id) : string
    {
        return $this->model->getNameById($id);
    }

    /**
     *  Get camera name by motion event Id
     */
    public function getNameByEventId(string $motionEventId) : string
    {
        return $this->model->getNameByEventId($motionEventId);
    }

    /**
     *  Get camera's configuration
     */
    public function getConfiguration(string $id) : array
    {
        return $this->model->getConfiguration($id);
    }

    /**
     *  Returns the total count of cameras
     */
    public function getTotal() : int
    {
        return count($this->getCamerasIds());
    }

    /**
     *  Returns all camera Id
     */
    public function getCamerasIds() : array
    {
        return $this->model->getCamerasIds();
    }

    /**
     *  Add a new camera
     */
    public function add(array $params) : void
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to add a new camera');
        }

        /**
         *  Get camera configuration template
         */
        $configuration = $this->cameraConfigController->getTemplate();
        $motionParams = [];
        $go2rtcStreams = [];
        $ffmpeg = false;
        $ffmpegParams = '';

        /**
         *  Get that minimal required parameters are set
         */
        Param\Name::check($params['name']);
        Param\Url::check($params['url']);
        Param\Resolution::check($params['resolution']);
        Param\Framerate::check($params['framerate']);
        Param\BasicAuthUsername::check($params['basic-auth-username']);
        Param\BasicAuthPassword::check($params['basic-auth-password']);

        /**
         *  Set camera configuration
         */
        $configuration['name']                    = $params['name'];
        $configuration['url']                     = $params['url'];
        $configuration['width']                   = explode('x', $params['resolution'])[0];
        $configuration['height']                  = explode('x', $params['resolution'])[1];
        $configuration['framerate']               = $params['framerate'];
        $configuration['motion-detection-enable'] = $params['motion-detection-enable'];
        $configuration['basic-auth-username']     = \Controllers\Common::validateData($params['basic-auth-username']);
        $configuration['basic-auth-password']     = \Controllers\Common::validateData($params['basic-auth-password']);

        /**
         *  Define proper stream URLs for go2rtc
         */
        $url = $params['url'];

        // If basic auth username and password are set, add them to the URL
        if (!empty($configuration['basic-auth-username']) and !empty($configuration['basic-auth-password'])) {
            $url = preg_replace('#://#i', '://' . $configuration['basic-auth-username'] . ':' . $configuration['basic-auth-password'] . '@', $url);
        }

        // First, add URL without filter to go2rtc
        // $go2rtcStreams[] = $url;

        // Case the URL is http(s)://
        if (preg_match('#^https?://#', $url)) {
            $ffmpeg = true;
            $ffmpegParams .= '#video=h264';
        // Case the URL is rtsp://
        } else if (preg_match('#^rtsp?://#', $url)) {
            $ffmpeg = true;
            $ffmpegParams .= '#video=h264#audio=opus';
        // Case the URL is /dev/video
        } else if (preg_match('#^/dev/video#', $url)) {
            $ffmpeg = true;
            $ffmpegParams .= '#video=h264';
        }

        // Frame rate
        if ($configuration['framerate'] > 0) {
            $ffmpeg = true;
            $ffmpegParams .= '#raw=-r ' . $configuration['framerate'];
        }

        // Add final URL with ffmpeg parameters if a filter has been set
        if ($ffmpeg) {
            $go2rtcStreams[] = 'ffmpeg:' . $url . $ffmpegParams;
        }

        /**
         *  Encode configuration to JSON
         */
        try {
            $configurationJson = json_encode($configuration, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception('Could not encode camera configuration to JSON');
        }

        /**
         *  Add camera in database
         */
        $this->model->add($configurationJson);

        /**
         *  Get new camera Id from database
         */
        $id = $this->model->getLastInsertRowID();

        if (empty($id)) {
            throw new Exception('Could not retrieve camera Id');
        }

        /**
         *  Prepare motion configuration parameters
         */
        $motionParams['device_id']   = ['status' => 'enabled', 'value' => $id];
        $motionParams['device_name'] = ['status' => 'enabled', 'value' => $configuration['name']];
        $motionParams['width']       = ['status' => 'enabled', 'value' => $configuration['width']];
        $motionParams['height']      = ['status' => 'enabled', 'value' => $configuration['height']];

        // Case the URL is http(s)://
        if (preg_match('#^https?://#', $url)) {
            $motionParams['netcam_url'] = ['status' => 'enabled', 'value' => 'rtsp://127.0.0.1:8554/camera_' . $id . '?mp4'];
            $motionParams['movie_passthrough'] = ['status' => 'enabled', 'value' => 'off'];
            $motionParams['v4l2_device'] = ['status' => 'disabled', 'value' => ''];
        // Case the URL is rtsp://
        } else if (preg_match('#^rtsp?://#', $url)) {
            $motionParams['netcam_url'] = ['status' => 'enabled', 'value' => 'rtsp://127.0.0.1:8554/camera_' . $id . '?video=h264&audio=opus'];
            $motionParams['movie_passthrough'] = ['status' => 'enabled', 'value' => 'on'];
            $motionParams['v4l2_device'] = ['status' => 'disabled', 'value' => ''];
        // Case the URL is /dev/video
        } else if (preg_match('#^/dev/video#', $url)) {
            $motionParams['netcam_url'] = ['status' => 'enabled', 'value' => 'rtsp://127.0.0.1:8554/camera_' . $id . '?mp4'];
            $motionParams['movie_passthrough'] = ['status' => 'enabled', 'value' => 'off'];
            $motionParams['v4l2_device'] = ['status' => 'disabled', 'value' => ''];
        }

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
        if ($configuration['motion-detection-enable'] == 'true') {
            $this->motionConfigController->enable($id);
        } else {
            $this->motionConfigController->disable($id);
        }

        /**
         *  Add a new stream in go2rtc
         */
        $this->go2rtcController->addStream($id, $go2rtcStreams);
    }

    /**
     *  Delete camera
     */
    public function delete(string $id) : void
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to delete a camera');
        }

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
            $this->motionServiceController->restart();
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
         *  Remove stream from go2rtc
         */
        $this->go2rtcController->removeStream($id);
    }

    /**
     *  Edit camera global settings
     */
    public function edit(int $id, array $params) : void
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to edit camera settings');
        }

        /**
         *  Check that camera Id exist
         */
        if (!$this->existId($id)) {
            throw new Exception('Camera does not exist');
        }

        /**
         *  Get camera configuration template
         */
        $configuration = $this->cameraConfigController->getTemplate();
        $motionParams = [];
        $go2rtcStreams = [];
        $ffmpeg = false;
        $ffmpegParams = '';

        /**
         *  Get that minimal required parameters are set
         */
        Param\Name::check($params['name']);
        Param\Url::check($params['url']);
        Param\Resolution::check($params['resolution']);
        Param\Framerate::check($params['framerate']);
        Param\Rotate::check($params['rotate']);
        Param\BasicAuthUsername::check($params['basic-auth-username']);
        Param\BasicAuthPassword::check($params['basic-auth-password']);
        Param\OnvifEnable::check($params['onvif-enable']);
        Param\OnvifPort::check($params['onvif-port']);
        // Param\OnvifUri::check($params['onvif-uri']);

        /**
         *  Set camera configuration
         */
        $configuration['name']                    = $params['name'];
        $configuration['url']                     = $params['url'];
        $configuration['width']                   = explode('x', $params['resolution'])[0];
        $configuration['height']                  = explode('x', $params['resolution'])[1];
        $configuration['framerate']               = $params['framerate'];
        $configuration['rotate']                  = $params['rotate'];
        $configuration['text-left']               = \Controllers\Common::validateData($params['text-left']);
        $configuration['text-right']              = \Controllers\Common::validateData($params['text-right']);
        $configuration['timestamp-left']          = \Controllers\Common::validateData($params['timestamp-left']);
        $configuration['timestamp-right']         = \Controllers\Common::validateData($params['timestamp-right']);
        $configuration['basic-auth-username']     = \Controllers\Common::validateData($params['basic-auth-username']);
        $configuration['basic-auth-password']     = \Controllers\Common::validateData($params['basic-auth-password']);
        $configuration['stream-enable']           = $params['stream-enable'];
        $configuration['motion-detection-enable'] = $params['motion-detection-enable'];
        $configuration['timelapse-enable']        = $params['timelapse-enable'];
        $configuration['hardware-acceleration']   = $params['hardware-acceleration'];
        $configuration['onvif']['enable']         = $params['onvif-enable'];
        $configuration['onvif']['port']           = $params['onvif-port'];
        // $configuration['onvif']['uri']            = $params['onvif-uri'];

        /**
         *  Try to generate Onvif Url
         */
        if ($params['onvif-enable'] == 'true') {
            // If the Url is a device, it cannot be moved
            if (preg_match('#/dev/video#', $params['url'])) {
                throw new Exception('Device camera are not movable');
            }

            // Parse camera URL to extract IP/hostname
            $parsedUrl = parse_url($params['url']);

            if ($parsedUrl === false or empty($parsedUrl)) {
                throw new Exception('Could not retrieve camera IP from URL');
            }

            if (empty($parsedUrl['host'])) {
                throw new Exception('Could not determine camera IP from URL');
            }

            $cameraIp = $parsedUrl['host'];

            // Build Onvif URL
            $onvifUrl = 'http://' . $cameraIp;

            // If a port is set, add it to the URL
            if (isset($params['onvif-port']) and $params['onvif-port'] > 0) {
                $onvifUrl .= ':' . $params['onvif-port'];
            }

            // If a URI is set, add it to the URL
            // if (!empty($params['onvif-uri'])) {
            //     $onvifUrl .= $params['onvif-uri'];
            // }

            $configuration['onvif']['url'] = $onvifUrl;
        }

        /**
         *  Define proper URL for go2rtc and motion
         */
        $url = $params['url'];

        // If basic auth username and password are set, add them to the URL
        if (!empty($configuration['basic-auth-username']) and !empty($configuration['basic-auth-password'])) {
            $url = preg_replace('#://#i', '://' . $configuration['basic-auth-username'] . ':' . $configuration['basic-auth-password'] . '@', $url);
        }

        // Case the URL is http(s)://
        if (preg_match('#^https?://#', $url)) {
            $ffmpeg = true;
            $ffmpegParams .= '#video=h264';
        // Case the URL is rtsp://
        } else if (preg_match('#^rtsp?://#', $url)) {
            $ffmpeg = true;
            $ffmpegParams .= '#video=h264#audio=opus';
        // Case the URL is /dev/video
        } else if (preg_match('#^/dev/video#', $url)) {
            $ffmpeg = true;
            $ffmpegParams .= '#video=h264';
        }

        // Frame rate
        if ($configuration['framerate'] > 0) {
            $ffmpeg = true;
            $ffmpegParams .= '#raw=-r ' . $configuration['framerate'];
        }

        // Rotate filter
        if ($configuration['rotate'] > 0) {
            $ffmpeg = true;
            $ffmpegParams .= '#rotate=' . $configuration['rotate'];
        }

        // Hardware acceleration
        if ($configuration['hardware-acceleration'] == 'true') {
            $ffmpeg = true;
            $ffmpegParams .= '#hardware';
        }

        // Add final URL with ffmpeg parameters if a filter has been set
        if ($ffmpeg) {
            $go2rtcStreams[] = 'ffmpeg:' . $url . $ffmpegParams;
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
         *  Encode configuration to JSON
         */
        try {
            $configurationJson = json_encode($configuration, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception('Could not encode camera configuration to JSON');
        }

        /**
         *  Edit global settings in database
         */
        $this->model->edit($id, $configurationJson);

        /**
         *  Prepare motion configuration parameters
         */
        $motionParams['device_id']   = ['status' => 'enabled', 'value' => $id];
        $motionParams['device_name'] = ['status' => 'enabled', 'value' => $configuration['name']];
        $motionParams['width']       = ['status' => 'enabled', 'value' => $configuration['width']];
        $motionParams['height']      = ['status' => 'enabled', 'value' => $configuration['height']];
        $motionParams['text_left']   = ['status' => 'enabled', 'value' => $configuration['text-left']];
        $motionParams['text_right']  = ['status' => 'enabled', 'value' => $configuration['text-right']];

        // Case the URL is http(s)://
        if (preg_match('#^https?://#', $url)) {
            $motionParams['netcam_url'] = ['status' => 'enabled', 'value' => 'rtsp://127.0.0.1:8554/camera_' . $id . '?mp4'];
            $motionParams['movie_passthrough'] = ['status' => 'enabled', 'value' => 'off'];
            $motionParams['v4l2_device'] = ['status' => 'disabled', 'value' => ''];
        // Case the URL is rtsp://
        } else if (preg_match('#^rtsp?://#', $url)) {
            $motionParams['netcam_url'] = ['status' => 'enabled', 'value' => 'rtsp://127.0.0.1:8554/camera_' . $id . '?video=h264&audio=opus'];
            $motionParams['movie_passthrough'] = ['status' => 'enabled', 'value' => 'on'];
            $motionParams['v4l2_device'] = ['status' => 'disabled', 'value' => ''];
        // Case the URL is /dev/video
        } else if (preg_match('#^/dev/video#', $url)) {
            $motionParams['netcam_url'] = ['status' => 'enabled', 'value' => 'rtsp://127.0.0.1:8554/camera_' . $id . '?mp4'];
            $motionParams['movie_passthrough'] = ['status' => 'enabled', 'value' => 'off'];
            $motionParams['v4l2_device'] = ['status' => 'disabled', 'value' => ''];
        }

        /**
         *  Edit camera motion configuration file
         */
        $this->motionConfigController->edit(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf', $motionParams);

        /**
         *  Enable / disable motion configuration file
         */
        if ($configuration['motion-detection-enable'] == 'true') {
            // Also force motion restart if a change was made in the camera configuration and if 'motion-detection-enable' is true
            $this->motionConfigController->enable($id);
        } else {
            $this->motionConfigController->disable($id);
        }

        /**
         *  Update go2rtc configuration for this stream
         */
        $this->go2rtcController->editStream($id, $go2rtcStreams);
    }

    /**
     *  Check if camera Id exist
     */
    public function existId(string $id) : bool
    {
        return $this->model->existId($id);
    }
}
