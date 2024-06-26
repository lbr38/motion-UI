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
    private $refresh;

    public function __construct()
    {
        $this->model = new \Models\Camera\Camera();
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
    public function add(string $name, string $url, string $streamUrl, string $outputType, string $outputResolution, string $refresh, string $liveEnable, string $motionEnable, string $timelapseEnable, string $username, string $password)
    {
        $mymotionService = new \Controllers\Motion\Service();

        /**
         *  Only allow certain caracters in URL
         */
        if (!\Controllers\Common::isAlphanumDash($url, array('&', '=', ':', '/', '.', '?', '&', '=', '@'))) {
            throw new Exception('URL contains invalid caracters');
        }
        if (!\Controllers\Common::isAlphanumDash($streamUrl, array('&', '=', ':', '/', '.', '?', '&', '=', '@'))) {
            throw new Exception('URL contains invalid caracters');
        }

        $name = \Controllers\Common::validateData($name);
        $url = \Controllers\Common::validateData($url);
        $streamUrl = \Controllers\Common::validateData($streamUrl);
        $outputType = \Controllers\Common::validateData($outputType);
        $outputResolution = \Controllers\Common::validateData($outputResolution);
        $refresh = \Controllers\Common::validateData($refresh);
        $liveEnable = \Controllers\Common::validateData($liveEnable);
        $motionEnable = \Controllers\Common::validateData($motionEnable);
        $timelapseEnable = \Controllers\Common::validateData($timelapseEnable);
        $username = \Controllers\Common::validateData($username);
        $password = \Controllers\Common::validateData($password);

        /**
         *  Check that URL starts with http(s):// or rtsp://
         */
        if (!preg_match('#(^https?://|^rtsp://)#', $url)) {
            throw new Exception('URL must start with <b>http(s)://</b> or <b>rtsp://</b>');
        }

        /**
         *  Check that output type is valid
         */
        if ($outputType != 'image' and $outputType != 'video') {
            throw new Exception('Specified output type is invalid');
        }

        /**
         *  Check that resolution is valid
         */
        if (!preg_match('#^([0-9]+)x([0-9]+)$#', $outputResolution)) {
            throw new Exception('Specified resolution is invalid');
        }

        if ($outputType == 'image' and $motionEnable == 'true' and empty($streamUrl)) {
            throw new Exception('Motion detection requires a stream URL');
        }

        /**
         *  If an additional stream URL is provided, check that it starts with http(s):// or rtsp://
         */
        if (!empty($streamUrl and !preg_match('#(^https?://)#', $streamUrl))) {
            throw new Exception('Stream URL must start with <b>http(s)://</b> or <b>rtsp://</b>');
        }

        if ($outputType == 'image') {
            if (!is_numeric($refresh)) {
                throw new Exception('Specified refresh rate is invalid');
            }
        }

        /**
         *  Add camera in database
         */
        $this->model->add($name, $url, $streamUrl, $outputType, $outputResolution, $liveEnable, $motionEnable, $timelapseEnable, $username, $password);

        /**
         *  Get inserted camera Id from database
         */
        $id = $this->getIdByName($name);

        if (empty($id)) {
            throw new Exception('Could not retrieve camera Id');
        }

        /**
         *  Motion configuration
         *  Generate a new motion configuration file for this camera
         */
        $this->generateMotionConfiguration($id);

        /**
         *  Restart motion service if running
         */
        if ($mymotionService->isRunning()) {
            if (!file_exists(DATA_DIR . '/motion.restart')) {
                touch(DATA_DIR . '/motion.restart');
            }
        }
    }

    /**
     *  Delete camera
     */
    public function delete(string $id)
    {
        $mymotionService = new \Controllers\Motion\Service();

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
        if (file_exists(CAMERAS_DIR . '/camera-' . $id . '.conf')) {
            if (!unlink(CAMERAS_DIR . '/camera-' . $id . '.conf')) {
                throw new Exception('Could not delete camera config file: ' . CAMERAS_DIR . '/camera-' . $id . '.conf');
            }
        }

        if (file_exists(CAMERAS_DIR . '/camera-' . $id . '.conf.disabled')) {
            if (!unlink(CAMERAS_DIR . '/camera-' . $id . '.conf.disabled')) {
                throw new Exception('Could not delete camera config file: ' . CAMERAS_DIR . '/camera-' . $id . '.conf.disabled');
            }
        }

        /**
         *  Delete camera data directory (timelapse images)
         */
        if (is_dir(DATA_DIR . '/cameras/camera-' . $id)) {
            if (!\Controllers\Filesystem\Directory::deleteRecursive(DATA_DIR . '/cameras/camera-' . $id)) {
                throw new Exception('Could not delete camera data directory: ' . DATA_DIR . '/cameras/camera-' . $id);
            }
        }

        /**
         *  Restart motion service if running
         */
        if ($mymotionService->isRunning()) {
            if (!file_exists(DATA_DIR . '/motion.restart')) {
                touch(DATA_DIR . '/motion.restart');
            }
        }
    }

    /**
     *  Edit camera global settings
     */
    public function editGlobalSettings(string $id, string $name, string $url, string $streamUrl, string $outputResolution, string $rotate, string $textLeft, string $textRight, string $liveEnable, string $motionEnable, string $timelapseEnable, string $username, string $password)
    {
        $mymotionService = new \Controllers\Motion\Service();

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
         *  Only allow certain caracters in URL
         */
        if (!\Controllers\Common::isAlphanumDash($url, array('=', ':', '/', '.', '?', '&', '=', '@'))) {
            throw new Exception('URL contains invalid caracters');
        }
        if (!\Controllers\Common::isAlphanumDash($streamUrl, array('=', ':', '/', '.', '?', '&', '=', '@'))) {
            throw new Exception('URL contains invalid caracters');
        }

        $name = \Controllers\Common::validateData($name);
        $url = \Controllers\Common::validateData($url);
        $streamUrl = \Controllers\Common::validateData($streamUrl);
        $outputResolution = \Controllers\Common::validateData($outputResolution);
        $rotate = \Controllers\Common::validateData($rotate);
        $textLeft = \Controllers\Common::validateData($textLeft);
        $textRight = \Controllers\Common::validateData($textRight);
        $liveEnable = \Controllers\Common::validateData($liveEnable);
        $motionEnable = \Controllers\Common::validateData($motionEnable);
        $timelapseEnable = \Controllers\Common::validateData($timelapseEnable);
        $username = \Controllers\Common::validateData($username);
        $password = \Controllers\Common::validateData($password);

        /**
         *  Check that URL starts with http(s):// or rtsp://
         */
        if (!preg_match('#(^https?://|^rtsp://)#', $url)) {
            throw new Exception('URL must start with <b>http(s)://</b> or <b>rtsp://</b>');
        }

        if ($actualConfiguration['Output_type'] == 'image' and $motionEnable == 'true' and empty($streamUrl)) {
            throw new Exception('Motion detection requires a stream URL');
        }

        /**
         *  Check that resolution is valid
         */
        if (!preg_match('#^([0-9]+)x([0-9]+)$#', $outputResolution)) {
            throw new Exception('Specified resolution is invalid');
        }

        /**
         *  If an additional stream URL is provided, check that it starts with http(s):// or rtsp://
         */
        if (!empty($streamUrl and !preg_match('#(^https?://)#', $streamUrl))) {
            throw new Exception('Stream URL must start with <b>http(s)://</b> or <b>rtsp://</b>');
        }

        if (!is_numeric($rotate)) {
            throw new Exception('Specified rotation is invalid');
        }

        if ($actualConfiguration['Motion_enabled'] == 'false') {
            $configFilePath = CAMERAS_DIR . '/camera-' . $id . '.conf.disabled';
        }
        if ($actualConfiguration['Motion_enabled'] == 'true') {
            $configFilePath = CAMERAS_DIR . '/camera-' . $id . '.conf';
        }

        /**
         *  Check if config file exist
         */
        if ($actualConfiguration['Motion_enabled'] == 'true') {
            if (!file_exists($configFilePath)) {
                throw new Exception('Cannot found camera configuration file');
            }
            if (!is_readable($configFilePath)) {
                throw new Exception('Camera configuration file is not readable');
            }
            if (!is_writeable($configFilePath)) {
                throw new Exception('Camera configuration file is not writeable');
            }
        }

        /**
         *  Edit global settings in database
         */
        $this->model->editGlobalSettings($id, $name, $url, $streamUrl, $outputResolution, $rotate, $textLeft, $textRight, $liveEnable, $motionEnable, $timelapseEnable, $username, $password);

        /**
         *  Edit global settings in motion config file
         */
        $configuration = file_get_contents($configFilePath);
        $configuration = preg_replace('/camera_name.*/i', 'camera_name ' . $name, $configuration);

        if ($actualConfiguration['Output_type'] == 'image') {
            if (!empty($streamUrl)) {
                $configuration = preg_replace('/netcam_url.*/i', 'netcam_url ' . $streamUrl, $configuration);
                $configuration = preg_replace('/netcam_high_url.*/i', 'netcam_high_url ' . $streamUrl, $configuration);
            }
        }
        if ($actualConfiguration['Output_type'] == 'video') {
            $configuration = preg_replace('/netcam_url.*/i', 'netcam_url ' . $url, $configuration);
            $configuration = preg_replace('/netcam_high_url.*/i', 'netcam_high_url ' . $url, $configuration);
        }

        /**
         *  Set width and height using resolution value
         */
        $resolution = explode('x', $outputResolution);
        $configuration = preg_replace('/width.*/i', 'width ' . $resolution[0], $configuration);
        $configuration = preg_replace('/height.*/i', 'height ' . $resolution[1], $configuration);

        /**
         *  Set rotation
         */
        $configuration = preg_replace('/rotate.*/i', 'rotate ' . $rotate, $configuration);

        /**
         *  Set text_left and text_right
         */
        if (!empty($textLeft)) {
            $configuration = preg_replace('/.*text_left.*/i', 'text_left ' . $textLeft, $configuration);
        } else {
            $configuration = preg_replace('/text_left.*/i', ';text_left ', $configuration);
        }
        if (!empty($textRight)) {
            $configuration = preg_replace('/.*text_right.*/i', 'text_right ' . $textRight, $configuration);
        } else {
            $configuration = preg_replace('/text_right.*/i', ';text_right ', $configuration);
        }

        /**
         *  If an username and password is specified
         */
        if (!empty($username) and !empty($password)) {
            $configuration = preg_replace('/.*netcam_userpass.*/i', 'netcam_userpass ' . $username . ':' . $password, $configuration);
        } else {
            $configuration = preg_replace('/.*netcam_userpass.*/i', ';netcam_userpass ', $configuration);
        }

        /**
         *  Write new configuration
         */
        if (!file_put_contents($configFilePath, $configuration)) {
            throw new Exception('Could not write to configuration file: ' . $configFilePath);
        }

        /**
         *  Rename config file if motion detection is enabled or disabled
         */
        if (file_exists(CAMERAS_DIR . '/camera-' . $id . '.conf.disabled') and $motionEnable == 'true') {
            if (!rename(CAMERAS_DIR . '/camera-' . $id . '.conf.disabled', CAMERAS_DIR . '/camera-' . $id . '.conf')) {
                throw new Exception('Could not enable motion detection for this camera');
            }
        }
        if (file_exists(CAMERAS_DIR . '/camera-' . $id . '.conf') and $motionEnable == 'false') {
            if (!rename(CAMERAS_DIR . '/camera-' . $id . '.conf', CAMERAS_DIR . '/camera-' . $id . '.conf.disabled')) {
                throw new Exception('Could not disable motion detection for this camera');
            }
        }

        /**
         *  Restart motion service if running
         */
        if ($mymotionService->isRunning()) {
            if (!file_exists(DATA_DIR . '/motion.restart')) {
                touch(DATA_DIR . '/motion.restart');
            }
        }
    }

    /**
     *  Edit camera stream settings
     */
    public function editStreamSettings(string $id, int $refresh = 3, string $timestampLeft = 'false', string $timestampRight = 'false')
    {
        $refresh = \Controllers\Common::validateData($refresh);
        $timestampLeft = \Controllers\Common::validateData($timestampLeft);
        $timestampRight = \Controllers\Common::validateData($timestampRight);

        if (!is_numeric($refresh)) {
            throw new Exception('Specified refresh rate is invalid');
        }

        if ($timestampLeft != 'false' and $timestampLeft != 'true') {
            throw new Exception('Specified timestamp left value is invalid');
        }

        if ($timestampRight != 'false' and $timestampRight != 'true') {
            throw new Exception('Specified timestamp right value is invalid');
        }

        /**
         *  Check if camera Id exist
         */
        if (!$this->existId($id)) {
            throw new Exception('Camera does not exist');
        }

        /**
         *  Edit stream settings in database
         */
        $this->model->editStreamSettings($id, $refresh, $timestampLeft, $timestampRight);
    }

    /**
     *  Check if camera Id exist
     */
    public function existId(string $id)
    {
        return $this->model->existId($id);
    }

    /**
     *  Generate motion configuration file
     */
    public function generateMotionConfiguration(string $id)
    {
        /**
         *  Generate motion.conf is not exist
         */
        $this->generationMotionMainConfiguration();

        /**
         *  Quit if a config file already exist
         */
        if (file_exists(CAMERAS_DIR . '/camera-' . $id . '.conf')) {
            return;
        }

        /**
         *  Check if camera Id exist
         */
        if (!$this->existId($id)) {
            throw new Exception('Camera does not exist');
        }

        /**
         *  Get camera configuration
         */
        $camera = $this->getConfiguration($id);

        /**
         *  Create camera's motion conf file from template
         */

        /**
         *  Create cameras dir if not exist
         */
        if (!is_dir(CAMERAS_DIR)) {
            throw new Exception('Cameras config dir does not exist: ' . CAMERAS_DIR);
        }

        /**
         *  First, delete file if already exist
         */
        if (file_exists(CAMERAS_DIR . '/camera-' . $id . '.conf')) {
            unlink(CAMERAS_DIR . '/camera-' . $id . '.conf');
        }

        /**
         *  Copy template
         */
        if (!copy(ROOT . '/templates/motion/motion-camera.conf', CAMERAS_DIR . '/camera-' . $id . '.conf')) {
            throw new Exception('Could not create camera config motion');
        }

        /**
         *  Set permissions
         */
        if (!chmod(CAMERAS_DIR . '/camera-' . $id . '.conf', octdec("0660"))) {
            throw new Exception('Could not set permissions on configuration file: ' . CAMERAS_DIR . '/camera-' . $id . '.conf');
        }
        if (!chgrp(CAMERAS_DIR . '/camera-' . $id . '.conf', 'motion')) {
            throw new Exception('Could not set group on configuration file: ' . CAMERAS_DIR . '/camera-' . $id . '.conf');
        }

        /**
         *  Replace values
         */
        $configuration = file_get_contents(CAMERAS_DIR . '/camera-' . $id . '.conf');
        $configuration = str_replace('__CAMERA_ID__', $id, $configuration);
        $configuration = str_replace('__CAMERA_NAME__', $camera['Name'], $configuration);

        if ($camera['Output_type'] == 'image' and !empty($camera['Stream_url'])) {
            $configuration = str_replace('__URL__', $camera['Stream_url'], $configuration);
        } else {
            $configuration = str_replace('__URL__', $camera['Url'], $configuration);
        }

        /**
         *  Set width and height using resolution value
         */
        $resolution = explode('x', $camera['Output_resolution']);
        $configuration = str_replace('__WIDTH__', $resolution[0], $configuration);
        $configuration = str_replace('__HEIGHT__', $resolution[1], $configuration);

        /**
         *  Set text_left and text_right
         */
        if (!empty($camera['Text_left'])) {
            $configuration = preg_replace('/.*text_left.*/i', 'text_left ' . $camera['Text_left'], $configuration);
        } else {
            $configuration = preg_replace('/text_left.*/i', ';text_left ', $configuration);
        }
        if (!empty($camera['Text_right'])) {
            $configuration = preg_replace('/.*text_right.*/i', 'text_right ' . $camera['Text_right'], $configuration);
        } else {
            $configuration = preg_replace('/text_right.*/i', ';text_right ', $configuration);
        }

        /**
         *  Set username and password for HTTP authentication if specified
         */
        if (!empty($camera['Username']) and !empty($camera['Password'])) {
            $configuration = preg_replace('/.*netcam_userpass.*/i', 'netcam_userpass ' . $camera['Username'] . ':' . $camera['Password'], $configuration);
        }

        /**
         *  Write to file
         */
        if (!file_put_contents(CAMERAS_DIR . '/camera-' . $id . '.conf', $configuration)) {
            throw new Exception('Could not write to configuration file: ' . CAMERAS_DIR . '/camera-' . $id . '.conf', $configuration);
        }

        if ($camera['Motion_enabled'] == 'false') {
            rename(CAMERAS_DIR . '/camera-' . $id . '.conf', CAMERAS_DIR . '/camera-' . $id . '.conf.disabled');
        }
    }

    /**
     *  Generate motion main configuration file
     */
    public function generationMotionMainConfiguration()
    {
        /**
         *  Copy motion.conf template if not exist
         */
        if (!file_exists('/etc/motion/motion.conf')) {
            if (!copy(ROOT . '/templates/motion/motion.conf', '/etc/motion/motion.conf')) {
                throw new Exception('Could not setup motion main config file: /etc/motion/motion.conf');
            }

            chmod('/etc/motion/motion.conf', octdec("0660"));
            chgrp('/etc/motion/motion.conf', 'motion');
        }
    }
}
