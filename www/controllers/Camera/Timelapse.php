<?php

namespace Controllers\Camera;

use Exception;

class Timelapse
{
    private $settingsController;
    private $cameraController;
    private $logController;
    private $cameras;

    public function __construct()
    {
        $this->settingsController = new \Controllers\Settings();
        $this->cameraController = new \Controllers\Camera\Camera();
        $this->logController = new \Controllers\Log\Log();
    }

    /**
     *  Get timelapse interval
     */
    private function getInterval()
    {
        $settings = $this->settingsController->get();

        return $settings['Timelapse_interval'];
    }

    /**
     *  Display timelapse section
     */
    public function display(int $cameraId, string|null $date = null, string|null $picture = null)
    {
        /**
         *  Get timelapse form
         */
        ob_start();
        include(ROOT . '/views/includes/camera/timelapse.inc.php');
        $form = ob_get_clean();

        return $form;
    }

    /**
     *  Return true if timelapse is enabled
     *  At least one camera must have timelapse enabled
     */
    public function enabled()
    {
        $this->cameras = $this->cameraController->get();

        foreach ($this->cameras as $camera) {
            try {
                $configuration = json_decode($camera['Configuration'], true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new Exception('Failed to decode camera #' . $camera['Id'] . ' configuration: ' . $e->getMessage());
            }

            if ($configuration['timelapse']['enable'] == 'true') {
                return true;
            }
        }

        return false;
    }

    /**
     *  Capture timelapse images
     */
    public function timelapse()
    {
        while (true) {
            pcntl_signal_dispatch();

            /**
             *  Always check if timelapse is still enabled before running
             */
            if ($this->enabled() !== true) {
                echo 'Timelapse is disabled on all cameras' . PHP_EOL;
                exit;
            }

            /**
             *  Always retrieve timelapse interval before running, in case it has been changed by the user
             */
            $timelapseInterval = $this->getInterval();

            /**
             *  Timelapse interval is in seconds
             *  Timelapse capture should match a correct time interval (e.g 01:00:00 or 01:00:05 or 01:05, etc.) depending on the timelapse interval
             *  Sleep until next interval if it is not the correct time
             */
            $nextInterval = ceil(time() / $timelapseInterval) * $timelapseInterval;

            if (time() < $nextInterval) {
                echo date('H:i:s') . ' - Sleeping until next timelapse interval' . PHP_EOL;
                sleep($nextInterval - time());
            }

            echo date('H:i:s') . ' - Running timelapse capture' . PHP_EOL;

            /**
             *  For each camera, if timelapse is enabled, execute timelapse
             */
            foreach ($this->cameras as $camera) {
                try {
                    try {
                        $configuration = json_decode($camera['Configuration'], true, 512, JSON_THROW_ON_ERROR);
                    } catch (JsonException $e) {
                        throw new Exception('failed to decode camera ' . $camera['Id'] . ' configuration: ' . $e->getMessage());
                    }

                    /**
                     *  Skip camera if timelapse is not enabled
                     */
                    if ($configuration['timelapse']['enable'] != 'true') {
                        continue;
                    }

                    /**
                     *  Create timelapse directory if it does not exist, with today date
                     */
                    $targetDir = CAMERAS_TIMELAPSE_DIR . '/camera-' . $camera['Id'] . '/' . date('Y-m-d');

                    if (!file_exists($targetDir)) {
                        if (!mkdir($targetDir, 0750, true)) {
                            throw new Exception('Failed to create timelapse directory: ' . $targetDir);
                        }
                    }

                    /**
                     *  Capture image
                     */
                    $content = file_get_contents('http://127.0.0.1:1984/api/frame.jpeg?src=camera_' . $camera['Id'], false, stream_context_create([
                        'http' => [
                            'timeout' => 3
                        ]
                    ]));

                    /**
                     *  Ignore if it fails to capture image because the camera may not be running 24/7
                     */
                    if ($content === false or empty($content)) {
                        continue;
                    }

                    /**
                     *  Save image to file
                     */
                    file_put_contents($targetDir . '/timelapse_' . date('H-i-s') . '.jpg', $content);
                } catch (Exception $e) {
                    $this->logController->log('error', 'Camera timelapse', 'Errow while executing timelapse capture: ' . $e->getMessage());
                }
            }

            sleep(1);
        }
    }

    /**
     *  Clean timelapse images older than specified date
     */
    public function clean(int $retention = 30)
    {
        $date = date('Y-m-d', strtotime('-' . $retention . ' days'));

        /**
         *  Get cameras
         */
        $this->cameras = $this->cameraController->get();

        /**
         *  For each camera, delete timelapse images older than specified date
         */
        foreach ($this->cameras as $camera) {
            $timelapseDir = CAMERAS_TIMELAPSE_DIR . '/camera-' . $camera['Id'];

            /**
             *  Skip camera if no timelapse directory
             */
            if (!file_exists($timelapseDir)) {
                continue;
            }

            /**
             *  Get all timelapse directories
             */
            $directories = glob($timelapseDir . '/*', GLOB_ONLYDIR);

            /**
             *  For each directory, delete if it is older than specified date
             */
            foreach ($directories as $directory) {
                $directoryDate = basename($directory);

                /**
                 *  Skip directory if it is not a date
                 */
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $directoryDate)) {
                    continue;
                }

                /**
                 *  Skip directory if it is not older than specified date
                 */
                if ($directoryDate >= $date) {
                    continue;
                }

                /**
                 *  Delete directory
                 */
                if (!\Controllers\Filesystem\Directory::deleteRecursive($directory)) {
                    $this->logController->log('error', 'Camera timelapse cleanup', 'Failed to delete timelapse directory: ' . $directory);
                }
            }
        }
    }
}
