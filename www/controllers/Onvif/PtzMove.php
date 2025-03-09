<?php

namespace Controllers\Onvif;

use Exception;

class PtzMove extends Ptz
{
    public function __construct(int $cameraId)
    {
        try {
            $cameraController = new \Controllers\Camera\Camera();

            /**
             *  Check if camera exists
             */
            if (!$cameraController->existId($cameraId)) {
                throw new Exception('Camera Id does not exist');
            }

            /**
             *  Get camera configuration
             */
            $configuration = $cameraController->getConfiguration($cameraId);

            try {
                $configuration = json_decode($configuration['Configuration'], true, 512, JSON_THROW_ON_ERROR);
            } catch (Exception $e) {
                throw new Exception('Could not decode configuration');
            }

            /**
             *  If camera is a device, it cannot be moved
             */
            if (preg_match('#/dev/video#', $configuration['main-stream']['device'])) {
                throw new Exception('Device camera are not movable');
            }

            /**
             *  Get camera Onvif url
             */
            if (empty($configuration['onvif']['url'])) {
                throw new Exception('Camera URL is not set');
            }

            $url = $configuration['onvif']['url'];

            if (empty($configuration['authentication']['username'])) {
                throw new Exception('Username is not set');
            }

            if (empty($configuration['authentication']['password'])) {
                throw new Exception('Password is not set');
            }

            /**
             *  Construct parent class
             */
            parent::__construct($url, $configuration['authentication']['username'], $configuration['authentication']['password']);

            if (empty($this->ptzUri)) {
                throw new Exception('PTZ URI is not set');
            }

            unset($cameraController, $configuration);
        } catch (Exception $e) {
            throw new Exception('Could not move camera: ' . $e->getMessage());
        }
    }

    /**
     *  Move camera
     */
    public function move(string $direction, string $moveType, float $moveSpeed) : void
    {
        if (!in_array($direction, ['up', 'down', 'left', 'right'])) {
            throw new Exception('Invalid direction');
        }

        if (!in_array($moveType, ['continuous', 'discontinuous'])) {
            throw new Exception('Invalid move type');
        }

        if ($moveSpeed < 0.1 or $moveSpeed > 1) {
            throw new Exception('Invalid move speed');
        }

        /**
         *  Get sources
         */
        $sources = $this->getSources();

        if (empty($sources[0][0]['profiletoken'])) {
            throw new Exception('Could not get profile token');
        }

        /**
         *  Get profile token
         */
        $profileToken = $sources[0][0]['profiletoken'];

        /**
         *  Move camera
         */
        if ($direction == 'up') {
            $this->ptzContinuousMove($profileToken, 0, $moveSpeed);
        } elseif ($direction == 'down') {
            $this->ptzContinuousMove($profileToken, 0, -$moveSpeed);
        } elseif ($direction == 'left') {
            $this->ptzContinuousMove($profileToken, -$moveSpeed, 0);
        } elseif ($direction == 'right') {
            $this->ptzContinuousMove($profileToken, $moveSpeed, 0);
        }

        if ($moveType == 'continuous') {
            return;
        }

        /**
         *  Wait for 1sec and stop camera movement
         */
        sleep(1);
        $this->ptzStop($profileToken, true, true);
    }

    /**
     *  Stop camera movement
     */
    public function stop(): void
    {
        /**
         *  Get sources
         */
        $sources = $this->getSources();

        if (empty($sources[0][0]['profiletoken'])) {
            throw new Exception('Could not get profile token');
        }

        /**
         *  Get profile token
         */
        $profileToken = $sources[0][0]['profiletoken'];

        /**
         *  Stop camera
         */
        $this->ptzStop($profileToken, true, true);
    }
}
