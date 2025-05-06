<?php

namespace Controllers\Camera;

use Exception;

class Stream extends Camera
{
    /**
     *  Enable or disable the camera stream
     */
    public function enable(int $id, string $enable) : void
    {
        if (!in_array($enable, ['true', 'false'])) {
            throw new Exception('Invalid stream status');
        }

        /**
         *  Get camera configuration
         */
        $configuration = $this->getConfiguration($id);

        /**
         *  Decode the configuration
         */
        try {
            $configuration = json_decode($configuration['Configuration'], true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception('Could not decode camera configuration from JSON');
        }

        /**
         *  Set the stream status
         */
        $configuration['stream']['enable'] = $enable;

        /**
         *  Encode the configuration
         */
        try {
            $configuration = json_encode($configuration, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception('Could not encode camera configuration to JSON');
        }

        /**
         *  Save the configuration
         */
        $this->model->saveGlobalConfiguration($id, $configuration);
    }
}
