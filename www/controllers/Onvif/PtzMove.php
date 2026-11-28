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
                throw new Exception(__('Camera Id does not exist'));
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
                throw new Exception(__('Device cameras are not movable'));
            }

            /**
             *  Get camera Onvif url
             */
            if (empty($configuration['onvif']['url'])) {
                throw new Exception(__('Camera URL is not set'));
            }

            $url = $configuration['onvif']['url'];

            if (empty($configuration['authentication']['username'])) {
                throw new Exception(__('Username is not set'));
            }

            if (empty($configuration['authentication']['password'])) {
                throw new Exception(__('Password is not set'));
            }

            /**
             *  Construct parent class
             */
            parent::__construct($url, $configuration['authentication']['username'], $configuration['authentication']['password'], self::getCacheFile($cameraId));

            unset($cameraController, $configuration);
        } catch (Exception $e) {
            throw new Exception(sprintf(__('Could not move camera: %s'), $e->getMessage()));
        }
    }

    /**
     *  Move camera
     */
    public function move(string $direction, string $moveType, float $moveSpeed): void
    {
        if (!in_array($direction, ['up', 'down', 'left', 'right'])) {
            throw new Exception(__('Invalid direction'));
        }

        if (!in_array($moveType, ['continuous', 'discontinuous'])) {
            throw new Exception(__('Invalid move type'));
        }

        if ($moveSpeed < 0.1 or $moveSpeed > 1) {
            throw new Exception(__('Invalid move speed'));
        }

        /**
         *  Pan/tilt velocity, depending on direction
         */
        $velocity = [
            'up'    => [0, $moveSpeed],
            'down'  => [0, -$moveSpeed],
            'left'  => [-$moveSpeed, 0],
            'right' => [$moveSpeed, 0]
        ][$direction];

        /**
         *  Move camera
         */
        $this->execute(function () use ($velocity) {
            $this->ptzContinuousMove($this->getProfileToken(), $velocity[0], $velocity[1]);
        });

        if ($moveType == 'continuous') {
            return;
        }

        /**
         *  Wait for 1sec and stop camera movement
         */
        sleep(1);
        $this->ptzStop($this->getProfileToken(), true, true);
    }

    /**
     *  Stop camera movement
     */
    public function stop(): void
    {
        $this->execute(function () {
            $this->ptzStop($this->getProfileToken(), true, true);
        });
    }

    /**
     *  Return the file used to cache the Onvif discovery data of a camera
     */
    public static function getCacheFile(int $cameraId): string
    {
        return CAMERAS_ONVIF_DIR . '/camera-' . $cameraId . '.json';
    }

    /**
     *  Get profile token of the first source
     */
    private function getProfileToken(): string
    {
        $sources = $this->getSources();

        if (empty($sources[0][0]['profiletoken'])) {
            throw new Exception(__('Could not get profile token'));
        }

        return $sources[0][0]['profiletoken'];
    }
}
