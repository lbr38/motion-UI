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
            if ($camera['Timelapse_enabled'] == 'true') {
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
                    /**
                     *  Skip camera if timelapse is not enabled
                     */
                    if ($camera['Timelapse_enabled'] != 'true') {
                        continue;
                    }

                    /**
                     *  Create timelapse directory if it does not exist, with today date
                     */
                    $targetDir = DATA_DIR . '/cameras/camera-' . $camera['Id'] . '/timelapse/' . date('Y-m-d');

                    if (!file_exists($targetDir)) {
                        if (!mkdir($targetDir, 0750, true)) {
                            throw new Exception('Failed to create timelapse directory: ' . $targetDir);
                        }
                    }

                    /**
                     *  Capture image
                     */

                    /**
                     *  Define ffmpeg command
                     *  Timeout is set to 3 seconds, kill after 5 seconds if it does not exit
                     */
                    $ffmpeg = '/usr/bin/timeout --kill-after=5 3 /usr/bin/ffmpeg';

                    /**
                     *  If camera has username and password, add it to the URL (format is http://username:password@url)
                     */
                    if (!empty($camera['Username']) and !empty($camera['Password'])) {
                        $ffmpeg .= ' -i ' . preg_replace('#://#i', '://' . $camera['Username'] . ':' . $camera['Password'] . '@', $camera['Url']);
                    } else {
                        $ffmpeg .= ' -i ' . $camera['Url'];
                    }

                    /**
                     *  If camera has rotate 180, add it to the command
                     */
                    if ($camera['Rotate'] == '180') {
                        $ffmpeg .= ' -vf "transpose=2,transpose=2"';
                    }

                    /**
                     *  Add output resolution
                     */
                    $ffmpeg .= ' -vframes 1 -video_size ' . $camera['Output_resolution'];

                    /**
                     *  Execute ffmpeg command and save to file
                     */
                    $myprocess = new \Controllers\Process($ffmpeg . ' ' . $targetDir . '/timelapse_' . date('H-i-s') . '.jpg >/dev/null 2>/dev/null &');
                    $myprocess->execute();
                    $output = $myprocess->getOutput();
                    $myprocess->close();

                    /**
                     *  Ignore error if ffmpeg fails to capture image because the camera may not be running 24/7
                     */
                    // if ($myprocess->getExitCode() != 0) {
                    //     throw new Exception('Failed to capture timelapse image for camera "' . $camera['Name'] . '" (ffmpeg error)');
                    // }
                } catch (Exception $e) {
                    $this->logController->log('error', 'Camera timelapse', 'Errow while executing timelapse capture: ' . $e->getMessage());
                }
            }

            sleep(1);
        }
    }
}
