<?php

namespace Controllers\Camera;

use Controllers\Utils\Validate;
use JsonException;
use Exception;

class Edit extends Camera
{
    /**
     *  Edit camera global settings
     */
    public function edit(int $id, array $params) : void
    {
        $go2rtcStreams = [];

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
         *  Get current configuration
         */
        $configuration = $this->getConfiguration($id);

        /**
         *  Get current camera configuration
         */
        try {
            $currentConfiguration = json_decode($configuration['Configuration'], true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception('Could not decode camera configuration from JSON');
        }

        /**
         *  Get current motion configuration
         */
        try {
            $motionConfiguration = json_decode($configuration['Motion_configuration'], true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception('Could not decode camera motion configuration from JSON');
        }

        /**
         *  Get camera configuration template
         */
        $configuration = $this->cameraConfigController->getTemplate();

        /**
         *  Check that minimal required parameters are set
         */
        Param\Name::check($params['name']);
        // Main stream
        Param\Device::check($params['main-stream-device']);
        Param\Resolution::check($params['main-stream-resolution']);
        Param\Framerate::check($params['main-stream-framerate']);
        Param\Rotate::check($params['main-stream-rotate']);
        // Secondary stream
        Param\Device::check($params['secondary-stream-device'], false);
        Param\Resolution::check($params['secondary-stream-resolution']);
        Param\Framerate::check($params['secondary-stream-framerate']);
        // Authentication
        Param\BasicAuthUsername::check($params['username']);
        Param\BasicAuthPassword::check($params['password']);
        Param\OnvifEnable::check($params['onvif-enable']);
        Param\OnvifPort::check($params['onvif-port']);
        // Monitoring
        Param\MonitoringEnable::check($params['monitoring-enable']);
        // Check that monitoring recipients are valid emails
        if ($params['monitoring-enable'] == 'true' and isset($params['monitoring-recipients']) and is_array($params['monitoring-recipients'])) {
            Param\MonitoringRecipients::check($params['monitoring-recipients']);
        }

        /**
         *  Set camera configuration
         */
        $configuration['name']                                 = $params['name'];
        // Main stream
        $configuration['main-stream']['device']                = $params['main-stream-device'];
        $configuration['main-stream']['resolution']            = $params['main-stream-resolution'];
        $configuration['main-stream']['width']                 = explode('x', $params['main-stream-resolution'])[0];
        $configuration['main-stream']['height']                = explode('x', $params['main-stream-resolution'])[1];
        $configuration['main-stream']['framerate']             = $params['main-stream-framerate'];
        $configuration['main-stream']['rotate']                = $params['main-stream-rotate'];
        $configuration['main-stream']['text-left']             = Validate::string($params['main-stream-text-left']);
        $configuration['main-stream']['text-right']            = Validate::string($params['main-stream-text-right']);
        $configuration['main-stream']['timestamp-left']        = Validate::string($params['main-stream-timestamp-left']);
        $configuration['main-stream']['timestamp-right']       = Validate::string($params['main-stream-timestamp-right']);
        // Secondary stream
        $configuration['secondary-stream']['device']           = $params['secondary-stream-device'];
        $configuration['secondary-stream']['resolution']       = $params['secondary-stream-resolution'];
        $configuration['secondary-stream']['width']            = explode('x', $params['secondary-stream-resolution'])[0];
        $configuration['secondary-stream']['height']           = explode('x', $params['secondary-stream-resolution'])[1];
        $configuration['secondary-stream']['framerate']        = $params['secondary-stream-framerate'];
        // Authentication
        $configuration['authentication']['username']           = Validate::string($params['username']);
        $configuration['authentication']['password']           = Validate::string($params['password']);
        $configuration['stream']['enable']                     = $currentConfiguration['stream']['enable'];
        $configuration['stream']['technology']                 = $params['stream-technology'];
        $configuration['motion-detection']['enable']           = $params['motion-detection-enable'];
        $configuration['timelapse']['enable']                  = $params['timelapse-enable'];
        $configuration['monitoring']['enable']                 = $params['monitoring-enable'];
        $configuration['monitoring']['recipients']             = $params['monitoring-recipients'] ?? [];
        $configuration['onvif']['enable']                      = $params['onvif-enable'];
        $configuration['onvif']['port']                        = $params['onvif-port'];

        /**
         *  Try to generate Onvif Url
         */
        if ($params['onvif-enable'] == 'true') {
            // If the Url is a device, it cannot be moved
            if (preg_match('#/dev/video#', $configuration['main-stream']['device'])) {
                throw new Exception('Device camera are not movable');
            }

            // Parse camera URL to extract IP/hostname
            $parsedUrl = parse_url($configuration['main-stream']['device']);

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

            $configuration['onvif']['url'] = $onvifUrl;
        }

        /**
         *  Define base motion configuration parameters
         */
        $motionConfiguration['device_id']['value']         = $id;
        $motionConfiguration['device_id']['enabled']       = true;
        $motionConfiguration['device_name']['value']       = $params['name'];
        $motionConfiguration['device_name']['enabled']     = true;
        // Authentication: default none and disabled
        $motionConfiguration['netcam_userpass']['value']   = '';
        $motionConfiguration['netcam_userpass']['enabled'] = false;

        // If auth username and password are set
        if (!empty($configuration['authentication']['username']) and !empty($configuration['authentication']['password'])) {
            $motionConfiguration['netcam_userpass']['value']   = $configuration['authentication']['username'] . ':' . $configuration['authentication']['password'];
            $motionConfiguration['netcam_userpass']['enabled'] = true;
        }

        /**
         *  Overwrite motion configuration parameters
         *  Do not overwrite if the parameter is locked
         */

        /**
         *  If a secondary stream is set, use some of its parameters it in priority, otherwise use the main stream
         */
        if (!empty($configuration['secondary-stream']['device'])) {
            $stream = 'secondary';
        } else {
            $stream = 'main';
        }

        if (empty($motionConfiguration['width']['locked']) or $motionConfiguration['width']['locked'] == 'false') {
            $motionConfiguration['width']['value'] = $configuration[$stream . '-stream']['width'];
            $motionConfiguration['width']['enabled'] = true;
        }
        if (empty($motionConfiguration['height']['locked']) or $motionConfiguration['height']['locked'] == 'false') {
            $motionConfiguration['height']['value'] = $configuration[$stream . '-stream']['height'];
            $motionConfiguration['height']['enabled'] = true;
        }
        if (empty($motionConfiguration['framerate']['locked']) or $motionConfiguration['framerate']['locked'] == 'false') {
            $motionConfiguration['framerate']['value'] = $configuration[$stream . '-stream']['framerate'];
            $motionConfiguration['framerate']['enabled'] = true;
        }
        if (empty($motionConfiguration['text_left']['locked']) or $motionConfiguration['text_left']['locked'] == 'false') {
            $motionConfiguration['text_left']['value'] = $configuration['main-stream']['text-left'];
            $motionConfiguration['text_left']['enabled'] = true;
        }
        if (empty($motionConfiguration['text_right']['locked']) or $motionConfiguration['text_right']['locked'] == 'false') {
            $motionConfiguration['text_right']['value'] = $configuration['main-stream']['text-right'];
            $motionConfiguration['text_right']['enabled'] = true;
        }
        if (empty($motionConfiguration['movie_container']['locked']) or $motionConfiguration['movie_container']['locked'] == 'false') {
            $motionConfiguration['movie_container']['value'] = 'mkv';
            $motionConfiguration['movie_container']['enabled'] = true;
        }

        // Case the URL is http(s)://
        if (preg_match('#^https?://#', $configuration[$stream . '-stream']['device'])) {
            if (empty($motionConfiguration['netcam_url']['locked']) or $motionConfiguration['netcam_url']['locked'] == 'false') {
                $motionConfiguration['netcam_url']['value'] = $configuration[$stream . '-stream']['device'];
                $motionConfiguration['netcam_url']['enabled'] = true;
            }

            // If a secondary stream is set, then main stream must be used for netcam_high_url
            if (!empty($configuration['secondary-stream']['device'])) {
                if (empty($motionConfiguration['netcam_high_url']['locked']) or $motionConfiguration['netcam_high_url']['locked'] == 'false') {
                    $motionConfiguration['netcam_high_url']['value'] = $configuration['main-stream']['device'];
                    $motionConfiguration['netcam_high_url']['enabled'] = true;
                }
            }

            if (empty($motionConfiguration['movie_passthrough']['locked']) or $motionConfiguration['movie_passthrough']['locked'] == 'false') {
                $motionConfiguration['movie_passthrough']['value'] = 'off';
                $motionConfiguration['movie_passthrough']['enabled'] = true;
            }

            if (empty($motionConfiguration['v4l2_device']['locked']) or $motionConfiguration['v4l2_device']['locked'] == 'false') {
                $motionConfiguration['v4l2_device']['value'] = '';
                $motionConfiguration['v4l2_device']['enabled'] = false;
            }
        // Case the URL is rtsp://
        } else if (preg_match('#^rtsp?://#', $configuration[$stream . '-stream']['device'])) {
            if (empty($motionConfiguration['netcam_url']['locked']) or $motionConfiguration['netcam_url']['locked'] == 'false') {
                $motionConfiguration['netcam_url']['value'] = $configuration[$stream . '-stream']['device'];
                $motionConfiguration['netcam_url']['enabled'] = true;
            }

            // If a secondary stream is set, then main stream must be used for netcam_high_url
            if (!empty($configuration['secondary-stream']['device'])) {
                if (empty($motionConfiguration['netcam_high_url']['locked']) or $motionConfiguration['netcam_high_url']['locked'] == 'false') {
                    $motionConfiguration['netcam_high_url']['value'] = $configuration['main-stream']['device'];
                    $motionConfiguration['netcam_high_url']['enabled'] = true;
                }
            }

            if (empty($motionConfiguration['movie_passthrough']['locked']) or $motionConfiguration['movie_passthrough']['locked'] == 'false') {
                $motionConfiguration['movie_passthrough']['value'] = 'on';
                $motionConfiguration['movie_passthrough']['enabled'] = true;
            }

            if (empty($motionConfiguration['v4l2_device']['locked']) or $motionConfiguration['v4l2_device']['locked'] == 'false') {
                $motionConfiguration['v4l2_device']['value'] = '';
                $motionConfiguration['v4l2_device']['enabled'] = false;
            }
        // Case the URL is /dev/video
        } else if (preg_match('#^/dev/video#', $configuration['main-stream']['device'])) {
            if (empty($motionConfiguration['netcam_url']['locked']) or $motionConfiguration['netcam_url']['locked'] == 'false') {
                $motionConfiguration['netcam_url']['value'] = $configuration['main-stream']['device'];
                $motionConfiguration['netcam_url']['enabled'] = true;
            }

            if (empty($motionConfiguration['movie_passthrough']['locked']) or $motionConfiguration['movie_passthrough']['locked'] == 'false') {
                $motionConfiguration['movie_passthrough']['value'] = 'off';
                $motionConfiguration['movie_passthrough']['enabled'] = true;
            }

            if (empty($motionConfiguration['v4l2_device']['locked']) or $motionConfiguration['v4l2_device']['locked'] == 'false') {
                $motionConfiguration['v4l2_device']['value'] = '';
                $motionConfiguration['v4l2_device']['enabled'] = false;
            }
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
         *  Encode motion configuration to JSON
         */
        try {
            $motionConfigurationJson = json_encode($motionConfiguration, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception('Could not encode camera motion configuration to JSON');
        }

        /**
         *  Save configurations in database
         */
        $this->saveGlobalConfiguration($id, $configurationJson);
        $this->saveMotionConfiguration($id, $motionConfigurationJson);

        /**
         *  Define proper stream URLs for go2rtc
         */
        $go2rtcStreams = $this->generateGo2rtcStreams($id, $configuration);

        /**
         *  Update go2rtc configuration for this stream
         */
        $this->go2rtcController->editStream($id, $go2rtcStreams);

        /**
         *  Edit camera motion configuration file
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

        unset($configuration, $configurationJson, $motionConfiguration, $motionConfigurationJson);
    }
}
