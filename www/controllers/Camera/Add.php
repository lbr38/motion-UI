<?php

namespace Controllers\Camera;

use Exception;

class Add extends Camera
{
    /**
     *  Add a new camera
     */
    public function add(array $params) : void
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to add a new camera');
        }

        $cameraStreamController = new \Controllers\Camera\Stream();
        $motionParams = [];
        $go2rtcStreams = [];
        $ffmpeg = false;
        $ffmpegParams = '';

        /**
         *  Check that minimal required parameters are set
         */
        Param\Name::check($params['name']);
        Param\Device::check($params['main-stream-device']);
        Param\Resolution::check($params['main-stream-resolution']);
        Param\Framerate::check($params['main-stream-framerate']);
        Param\BasicAuthUsername::check($params['username']);
        Param\BasicAuthPassword::check($params['password']);

        /**
         *  Get camera configuration template
         */
        $configuration = $this->cameraConfigController->getTemplate();

        /**
         *  Set camera global configuration
         */
        $configuration['name']                       = $params['name'];
        $configuration['main-stream']['device']      = $params['main-stream-device'];
        $configuration['main-stream']['width']       = explode('x', $params['main-stream-resolution'])[0];
        $configuration['main-stream']['height']      = explode('x', $params['main-stream-resolution'])[1];
        $configuration['main-stream']['framerate']   = $params['main-stream-framerate'];
        $configuration['authentication']['username'] = \Controllers\Common::validateData($params['username']);
        $configuration['authentication']['password'] = \Controllers\Common::validateData($params['password']);
        $configuration['motion-detection']['enable'] = $params['motion-detection-enable'];
        // If camera is a http:// camera, use mjpeg mode for streaming
        if (preg_match('#^https?://#', $configuration['main-stream']['device'])) {
            $configuration['stream']['technology'] = 'mjpeg';
        }

        /**
         *  Add camera in database
         */
        $this->model->add();

        /**
         *  Get new camera Id from database
         */
        $id = $this->model->getLastInsertRowID();

        if (empty($id)) {
            throw new Exception('Could not retrieve camera Id');
        }

        /**
         *  Get camera motion configuration template
         */
        $motionConfiguration = $this->motionTemplateController->get($id);

        /**
         *  Prepare motion configuration parameters
         */
        $motionConfiguration['device_id']       = ['enabled' => true, 'value' => $id];
        $motionConfiguration['device_name']     = ['enabled' => true, 'value' => $params['name']];
        $motionConfiguration['width']           = ['enabled' => true, 'value' => explode('x', $params['main-stream-resolution'])[0]];
        $motionConfiguration['height']          = ['enabled' => true, 'value' => explode('x', $params['main-stream-resolution'])[1]];
        $motionConfiguration['framerate']       = ['enabled' => true, 'value' => $params['main-stream-framerate']];

        // If auth username and password are set
        if (!empty($configuration['authentication']['username']) and !empty($configuration['authentication']['password'])) {
            $motionConfiguration['netcam_userpass']['value'] = $configuration['authentication']['username'] . ':' . $configuration['authentication']['password'];
            $motionConfiguration['netcam_userpass']['enabled'] = true;
        }

        // Case the URL is http(s)://
        if (preg_match('#^https?://#', $configuration['main-stream']['device'])) {
            $motionConfiguration['netcam_url'] = ['enabled' => true, 'value' => $configuration['main-stream']['device']];
            $motionConfiguration['movie_passthrough'] = ['enabled' => false, 'value' => 'on'];
            $motionConfiguration['v4l2_device'] = ['enabled' => false, 'value' => ''];
        // Case the URL is rtsp://
        } else if (preg_match('#^rtsp?://#', $configuration['main-stream']['device'])) {
            $motionConfiguration['netcam_url'] = ['enabled' => true, 'value' => $configuration['main-stream']['device']];
            $motionConfiguration['movie_passthrough'] = ['enabled' => true, 'value' => 'on'];
            $motionConfiguration['v4l2_device'] = ['enabled' => false, 'value' => ''];
        // Case the URL is /dev/video
        } else if (preg_match('#^/dev/video#', $configuration['main-stream']['device'])) {
            // /dev/videoX devices cannot be used by both go2rtc and motion at the same time (device is locked), so force the use of the go2rtc stream
            $motionConfiguration['netcam_url'] = ['enabled' => true, 'value' => 'rtsp://127.0.0.1:8554/camera_' . $id . '?mp4'];
            $motionConfiguration['movie_passthrough'] = ['enabled' => false, 'value' => 'on'];
            $motionConfiguration['v4l2_device'] = ['enabled' => true, 'value' => $configuration['main-stream']['device']];
        }

        /**
         *  Encode global configuration to JSON
         */
        try {
            $configurationJson = json_encode($configuration, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception('Could not encode camera configuration to JSON');
        }

        /**
         *  Encode motion configuration to JSON
         */
        try {
            $motionConfigurationJson = json_encode($motionConfiguration, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception('Could not encode camera configuration to JSON');
        }

        /**
         *  Save configurations to database
         */
        $this->model->saveGlobalConfiguration($id, $configurationJson);
        $this->model->saveMotionConfiguration($id, $motionConfigurationJson);

        /**
         *  Edit motion configuration file for this camera
         */
        $this->motionConfigController->write(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf', $motionConfiguration);

        /**
         *  Enable / disable motion configuration file
         */
        if ($configuration['motion-detection']['enable'] == 'true') {
            $this->motionConfigController->enable($id);
        } else {
            $this->motionConfigController->disable($id);
        }

        /**
         *  Define proper stream URLs for go2rtc
         */
        $go2rtcStreams = $this->generateGo2rtcStreams($id, $configuration);

        /**
         *  Add a new stream in go2rtc
         */
        $this->go2rtcController->addStream($id, $go2rtcStreams);

        /**
         *  Add new camera Id to the order
         */
        $cameraStreamController->addToOrder($id);

        unset($configuration, $configurationJson, $motionConfiguration, $motionConfigurationJson, $cameraStreamController);
    }
}
