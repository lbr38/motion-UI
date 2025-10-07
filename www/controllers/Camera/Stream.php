<?php

namespace Controllers\Camera;

use Exception;
use JsonException;

class Stream
{
    private $model;
    private $cameraController;

    public function __construct()
    {
        $this->model = new \Models\Camera\Stream();
        $this->cameraController = new \Controllers\Camera\Camera();
    }

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
        $configuration = $this->cameraController->getConfiguration($id);

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
        $this->cameraController->saveGlobalConfiguration($id, $configuration);
    }

    /**
     *  Get the camera grid order
     */
    public function getOrder() : array
    {
        $order = $this->model->getOrder();

        if (empty($order)) {
            return [];
        }

        try {
            return json_decode($order, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException|Exception $e) {
            throw new Exception('Could not decode camera order from JSON');
        }
    }

    /**
     *  Return stream informations (resolution, framerate)
     */
    public function getInfo(string $url)
    {
        $data = [];

        $process = new \Controllers\Process('/usr/bin/ffprobe -loglevel quiet -select_streams v:0 -show_entries stream=width,height,r_frame_rate -of default=noprint_wrappers=1 ' . escapeshellarg($url));
        $process->execute();
        $output = trim($process->getOutput());
        $process->close();

        if ($process->getExitCode() != 0) {
            throw new Exception('Could not get stream info from URL: ' . $output);
        }

        if (empty($output)) {
            throw new Exception('No stream info found at the URL');
        }

        $lines = explode("\n", $output);

        foreach ($lines as $line) {
            [$key, $value] = explode('=', $line);
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     *  Sort the camera grid
     */
    public function sort(array $order) : void
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to sort the cameras');
        }

        /**
         *  Clean empty values from the order
         */
        $order = array_filter($order);

        /**
         *  Check that the order is an array of integers
         */
        foreach ($order as $key => $value) {
            if (!is_numeric($value)) {
                throw new Exception('Invalid order value: ' . $value);
            }
        }

        /**
         *  Encode the order to JSON
         */
        try {
            $order = json_encode($order, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception('Could not encode camera sort order to JSON');
        }

        /**
         *  Save the order in the database
         */
        $this->model->sort($order);
    }

    /**
     *  Add camera Id to the grid order
     */
    public function addToOrder(int $id) : void
    {
        /**
         *  Get current cameras order
         */
        $order = $this->getOrder();

        /**
         *  If the camera Id already exists in the order, nothing to add
         */
        if (in_array($id, $order)) {
            return;
        }

        /**
         *  Add new camera Id to the order
         */
        $order[] = $id;

        /**
         *  Save the new order
         */
        try {
            $this->sort(array_map('strval', $order)); // Convert all values to strings
        } catch (Exception $e) {
            throw new Exception('Could not save camera order: ' . $e->getMessage());
        }
    }

    /**
     *  Remove camera Id from the grid order
     */
    public function removeFromOrder(int $id) : void
    {
        /**
         *  Get current cameras order
         */
        $order = $this->getOrder();

        /**
         *  If there is no current order, nothing to remove
         */
        if (empty($order)) {
            return;
        }

        /**
         *  If the camera Id does not exist in the order, nothing to remove
         */
        if (($key = array_search($id, $order)) === false) {
            return;
        }

        /**
         *  Remove camera Id from order array if it exists
         */
        unset($order[$key]);

        /**
         *  Save the new order
         */
        try {
            $this->sort($order);
        } catch (Exception $e) {
            throw new Exception('Could not save camera order: ' . $e->getMessage());
        }

        unset($order);
    }
}
