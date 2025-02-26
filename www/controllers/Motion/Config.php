<?php

namespace Controllers\Motion;

use Exception;

class Config
{
    private $motionServiceController;

    public function __construct()
    {
        $this->motionServiceController = new \Controllers\Motion\Service();
    }

    /**
     *  Return motion configuration params from a file
     */
    public function getConfig(string $file) : array
    {
        $currentParams = [];

        /**
         *  Get current params in the file
         */
        if (!is_readable($file)) {
            throw new Exception('Could not read configuration file: ' . $file);
        }

        $content = file_get_contents($file);

        if ($content === false) {
            throw new Exception('Could not retrieve configuration file content: ' . $file);
        }

        /**
         *  Split lines
         */
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            $status = 'enabled';
            $param = '';
            $value = '';

            $line = trim($line);

            // Ignore line if it's a comment
            if (preg_match('/^#/', $line)) {
                continue;
            }

            // Ignore line if empty
            if (empty($line)) {
                continue;
            }

            /**
             *  Split line in key and value
             *  eg. key value
             *  First word is the key, the rest is the value
             */
            $line = explode(' ', $line, 2);
            $param = trim($line[0]);
            if (isset($line[1])) {
                $value = trim($line[1]);
            }

            /**
             *  If param starts with ';' then it is disabled
             */
            if (preg_match('/^;/', $param)) {
                $status = 'disabled';
                $param = preg_replace('/^;/', '', $param);
            }

            /**
             *  Add param to currentParams
             */
            $currentParams[$param] = [
                'status' => $status,
                'value' => $value
            ];
        }

        return $currentParams;
    }

    /**
     *  Generate motion configuration file
     */
    private function generateMainConfig() : void
    {
        /**
         *  If /etc/motion/motion.conf already exist, then quit
         */
        if (file_exists('/etc/motion/motion.conf')) {
            return;
        }

        /**
         *  Get default motion configuration template
         */
        $params = \Controllers\Motion\Template::getMainParamsTemplate();

        /**
         *  Write default params to /etc/motion/motion.conf
         */
        $this->write('/etc/motion/motion.conf', $params);

        /**
         *  Set permissions
         */
        chmod('/etc/motion/motion.conf', octdec("0660"));
        chgrp('/etc/motion/motion.conf', 'motionui');
    }

    /**
     *  Generate motion camera configuration file
     */
    public function generateCameraConfig(string $id) : void
    {
        /**
         *  Generate /etc/motion/motion.conf if not exist
         */
        $this->generateMainConfig();

        /**
         *  Quit if a config file already exist for this camera
         */
        if (file_exists(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf')) {
            return;
        }

        /**
         *  Get default camera configuration template
         */
        $params = \Controllers\Motion\Template::getCameraParamsTemplate($id);

        /**
         *  Write default params to camera-<id>.conf
         */
        $this->write(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf', $params);

        /**
         *  Set permissions
         */
        if (!chmod(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf', octdec("0660"))) {
            throw new Exception('Could not set permissions on configuration file: ' . CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf');
        }
        if (!chgrp(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf', 'motionui')) {
            throw new Exception('Could not set group on configuration file: ' . CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf');
        }
    }

    /**
     *  Edit motion configuration file
     */
    public function edit(string $file, array $params) : void
    {
        /**
         *  First get current params
         */
        $currentParams = $this->getConfig($file);

        /**
         *  Edit and overwrite current params with new params
         */
        foreach ($params as $param => $details) {
            // Ignore if param is empty
            if (empty($param)) {
                continue;
            }

            // Ignore if param value is not set
            if (!isset($details['value'])) {
                continue;
            }

            $status = $details['status'];
            $value  = $details['value'];

            // If status is 'removed', then remove the param from current params
            if ($status == 'removed') {
                if (isset($currentParams[$param])) {
                    unset($currentParams[$param]);
                }

                continue;
            }

            $currentParams[$param] = [
                'status' => $status,
                'value' => $value
            ];
        }

        /**
         *  Order params
         */
        ksort($currentParams);

        /**
         *  Write new configuration
         */
        $this->write($file, $currentParams);
    }

    /**
     *  Write motion configuration to file
     */
    public function write(string $file, array $params) : void
    {
        $content = '';

        foreach ($params as $name => $details) {
            $name   = \Controllers\Common::validateData($name);
            $status = $details['status'];
            $value  = $details['value'];

            /**
             *  Check that parameter name is valid and does not contains invalid characters
             */
            if (\Controllers\Common::isAlphanumDash($name) === false) {
                throw new Exception($name . ' parameter name contains invalid character(s)');
            }

            /**
             *  Case the option is 'netcam_url' or 'netcam_high_url'
             */
            if ($status == 'enabled' and ($name == 'netcam_url' or $name == 'netcam_high_url')) {
                /**
                 *  Check that URL starts with http:// or https:// or rtsp://
                 */
                if (!preg_match('#((^https?|rtsp)://)#', $value)) {
                    throw new Exception('<b>' . $name . '</b> parameter value must start with http:// or https:// or rtsp://');
                }
            }

            if ($status == 'enabled') {
                $status = '';
            } else {
                $status = ';';
            }

            $content .= $status . htmlspecialchars_decode($name) . " " . htmlspecialchars_decode($value) . PHP_EOL . PHP_EOL;
        }

        /**
         *  Write new params to file
         */
        if (!file_put_contents($file, trim($content))) {
            throw new Exception('Could not write to configuration file: ' . $file);
        }

        unset($content);
    }

    /**
     *  Edit motion configuration file (in /var/lib/motionui/cameras/)
     */
    public function configure(string $cameraId, array $params) : void
    {
        $file = CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $cameraId . '.conf';

        if (!file_exists($file)) {
            throw new Exception('Camera configuration file does not exist: ' . $file);
        }

        /**
         *  Edit and overwrite current params with new params
         */
        $this->edit($file, $params);

        /**
         *  Restart motion service if running
         */
        $this->motionServiceController->restart();
    }

    /**
     *  Delete parameter from motion configuration file
     */
    public function deleteParameter(int $id, string $param) : void
    {
        $file = CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf';

        /**
         *  First get current params
         */
        $currentParams = $this->getConfig($file);

        /**
         *  Delete param from current params
         */
        if (isset($currentParams[$param])) {
            unset($currentParams[$param]);
        }

        /**
         *  Order params
         */
        ksort($currentParams);

        /**
         *  Write new configuration
         */
        $this->write($file, $currentParams);

        /**
         *  Restart motion service if running
         */
        $this->motionServiceController->restart();
    }

    /**
     *  Enable motion camera configuration file
     */
    public function enable(int $id) : void
    {
        $filename_available = CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf';
        $filename_enabled = CAMERAS_MOTION_CONF_ENABLED_DIR . '/camera-' . $id . '.conf';

        if (!file_exists($filename_available)) {
            throw new Exception('Camera configuration file does not exist');
        }

        /**
         *  Enable camera configuration file
         */
        if (!file_exists($filename_enabled)) {
            if (!symlink($filename_available, $filename_enabled)) {
                throw new Exception('Could not enable camera configuration file');
            }
        }

        /**
         *  Restart motion service if running
         */
        $this->motionServiceController->restart();
    }

    /**
     *  Disable motion camera configuration file
     */
    public function disable(int $id) : void
    {
        $filename_available = CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf';
        $filename_enabled = CAMERAS_MOTION_CONF_ENABLED_DIR . '/camera-' . $id . '.conf';

        if (!file_exists($filename_available)) {
            throw new Exception('Camera configuration file does not exist');
        }

        /**
         *  Ignore if camera configuration file is already disabled
         */
        if (!file_exists($filename_enabled)) {
            return;
        }

        /**
         *  Disable camera configuration file
         */
        if (!unlink($filename_enabled)) {
            throw new Exception('Could not disable camera configuration file');
        }

        /**
         *  Restart motion service if running
         */
        $this->motionServiceController->restart();
    }
}
