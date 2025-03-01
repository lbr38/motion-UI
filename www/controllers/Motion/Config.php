<?php

namespace Controllers\Motion;

use Exception;
use JsonException;

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
     *  Edit motion configuration file
     */
    public function edit(string $file, array $params) : void
    {
        /**
         *  Order params
         */
        ksort($params);

        /**
         *  Write new configuration
         */
        $this->write($file, $params);
    }

    /**
     *  Write motion configuration to file
     */
    public function write(string $file, array $params) : void
    {
        $content = '';

        /**
         *  If configuration file already exist, check if it is readable and writeable
         */
        if (file_exists($file)) {
            if (!is_readable($file)) {
                throw new Exception('Motion configuration file is not readable: ' . $file);
            }
            if (!is_writeable($file)) {
                throw new Exception('Motion configuration file is not writeable: ' . $file);
            }
        }

        /**
         *  Sort params
         */
        ksort($params);

        foreach ($params as $name => $details) {
            $name    = \Controllers\Common::validateData($name);
            $enabled = $details['enabled'];
            $value   = $details['value'];

            /**
             *  Check that parameter name is valid and does not contains invalid characters
             */
            if (\Controllers\Common::isAlphanumDash($name) === false) {
                throw new Exception($name . ' parameter name contains invalid character(s)');
            }

            if ($enabled == 'true') {
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
        $cameraController = new \Controllers\Camera\Camera();

        $file = CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $cameraId . '.conf';

        /**
         *  Write new configuration to file
         */
        $this->write($file, $params);

        /**
         *  Convert params to JSON
         */
        try {
            $paramsJson = json_encode($params, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception('Could not encode params to JSON: ' . $e->getMessage());
        }

        /**
         *  Save new configuration to database
         */
        $cameraController->saveMotionConfiguration($cameraId, $paramsJson);

        /**
         *  Restart motion service if running
         */
        $this->motionServiceController->restart();

        unset($cameraController, $params, $paramsJson);
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
