<?php

namespace Controllers\Camera;

use Exception;

class Camera
{
    protected $model;
    protected $cameraConfigController;
    protected $motionConfigController;
    protected $motionTemplateController;
    protected $motionServiceController;
    protected $go2rtcController;

    public function __construct()
    {
        $this->model = new \Models\Camera\Camera();
        $this->cameraConfigController = new \Controllers\Camera\Config();
        $this->motionConfigController = new \Controllers\Motion\Config();
        $this->motionTemplateController = new \Controllers\Motion\Template();
        $this->motionServiceController = new \Controllers\Motion\Service();
        $this->go2rtcController = new \Controllers\Go2rtc\Go2rtc();
    }

    /**
     *  Get all cameras
     */
    public function get() : array
    {
        return $this->model->get();
    }

    /**
     *  Get camera name by its Id
     */
    public function getNameById(string $id) : string
    {
        return $this->model->getNameById($id);
    }

    /**
     *  Get camera name by motion event Id
     */
    public function getNameByEventId(string $motionEventId) : string
    {
        return $this->model->getNameByEventId($motionEventId);
    }

    /**
     *  Get camera's configuration
     */
    public function getConfiguration(int $id) : array
    {
        return $this->model->getConfiguration($id);
    }

    /**
     *  Returns the total count of cameras
     */
    public function getTotal() : int
    {
        return count($this->getCamerasIds());
    }

    /**
     *  Returns all camera Id
     */
    public function getCamerasIds() : array
    {
        return $this->model->getCamerasIds();
    }

    /**
     *  Save camera's global configuration
     */
    public function saveGlobalConfiguration(string $id, string $configuration) : void
    {
        $this->model->saveGlobalConfiguration($id, $configuration);
    }

    /**
     *  Save camera's motion configuration
     */
    public function saveMotionConfiguration(string $id, string $configuration) : void
    {
        $this->model->saveMotionConfiguration($id, $configuration);
    }

    /**
     *  Check if camera Id exist
     */
    public function existId(string $id) : bool
    {
        return $this->model->existId($id);
    }

    /**
     *  Generate en return go2rtc streams from configuration
     */
    public function generateGo2rtcStreams(int $id, array $configuration)
    {
        $streams = [];
        $primaryStreamFFmpegParams = [];
        $primaryStream = $configuration['main-stream']['device'];

        /**
         *  If basic auth username and password are set, add them to the URL
         */
        if (!empty($configuration['authentication']['username']) and !empty($configuration['authentication']['password'])) {
            $primaryStream = preg_replace('#://#i', '://' . $configuration['authentication']['username'] . ':' . $configuration['authentication']['password'] . '@', $primaryStream);
        }

        /**
         *  If primary stream is a /dev/videoX device, force the use of ffmpeg to stream it
         */
        if (preg_match('#^/dev/video#', $primaryStream)) {
            $primaryStreamFFmpegParams[] = '#video=h264';
        }

        // Rotate filter
        if ($configuration['main-stream']['rotate'] > 0) {
            // rotate requires cannot be used with the same video output as the source (aka #video=copy), so force the use of a video codec
            $primaryStreamFFmpegParams[] = '#video=h264';
            $primaryStreamFFmpegParams[] = '#rotate=' . $configuration['main-stream']['rotate'];
        }

        /**
         *  First, add native URL to streams
         *  If filter(s) are set, use ffmpeg to stream it
         */
        if (!empty($primaryStreamFFmpegParams)) {
            $primaryStreamFFmpegParams = array_unique($primaryStreamFFmpegParams);
            $streams[] = 'ffmpeg:' . $primaryStream . implode('', $primaryStreamFFmpegParams);
        } else {
            $streams[] = $primaryStream;
        }

        /**
         *  Generate secondary streams
         */

        // Case the URL is http(s)://
        if (preg_match('#^https?://#', $primaryStream)) {
            $streams[] = 'ffmpeg:camera_' . $id . '#video=h264#hardware';
        // Case the URL is rtsp://
        } else if (preg_match('#^rtsp?://#', $primaryStream)) {
            $streams[] = 'ffmpeg:camera_' . $id . '#video=h264#hardware';
            $streams[] = 'ffmpeg:camera_' . $id . '#video=mjpeg#hardware';
            $streams[] = 'ffmpeg:camera_' . $id . '#audio=opus';
        }

        return $streams;
    }
}
