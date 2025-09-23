<?php

namespace Controllers\Service\Unit;

use JsonException;
use Exception;

class Timelapse extends \Controllers\Service\Service
{
    private $timelapseController;
    private $cameraController;

    public function __construct(string $unit)
    {
        parent::__construct($unit);

        $this->timelapseController = new \Controllers\Camera\Timelapse();
        $this->cameraController = new \Controllers\Camera\Camera();
    }

    /**
     *  Run timelapse
     */
    public function run() : void
    {
        parent::log('Starting timelapse');

        // Get list of cameras
        $cameras = $this->cameraController->get();

        while (true) {
            // Always retrieve timelapse interval before running, in case it has been changed by the user
            $timelapseInterval = parent::getSettings('Timelapse_interval');

            /**
             *  Timelapse interval is in seconds
             *  Timelapse capture should match a correct time interval (e.g 01:00:00 or 01:00:05 or 01:05, etc.) depending on the timelapse interval
             *  Sleep until next interval if it is not the correct time
             */
            $nextInterval = ceil(time() / $timelapseInterval) * $timelapseInterval;

            // Wait until next timelapse interval
            if (time() < $nextInterval) {
                sleep($nextInterval - time());
            }

            // For each camera, if timelapse is enabled, execute timelapse
            foreach ($cameras as $camera) {
                try {
                    try {
                        $configuration = json_decode($camera['Configuration'], true, 512, JSON_THROW_ON_ERROR);
                    } catch (JsonException $e) {
                        throw new Exception('failed to decode JSON camera configuration: ' . $e->getMessage());
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
                            throw new Exception('failed to create timelapse directory: ' . $targetDir);
                        }
                    }

                    parent::log('Capture timelapse for camera #' . $camera['Id'] . ' (' . $configuration['name'] . ')');

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
                    $file = $targetDir . '/timelapse_' . date('H-i-s') . '.jpg';
                    if (!file_put_contents($file, $content)) {
                        throw new Exception('failed to save timelapse image to file: ' . $file);
                    }
                } catch (Exception $e) {
                    // Log error but continue with next camera
                    parent::logError('Error while executing timelapse capture for camera #' . $camera['Id'] . ': ' . $e->getMessage());
                }
            }

            sleep(1);
        }
    }
}
