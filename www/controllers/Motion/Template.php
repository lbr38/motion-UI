<?php

namespace Controllers\Motion;

use Exception;

class Template
{
    /**
     *  Return main configuration params template
     *  /etc/motion/motion.conf
     */
    public static function getMainParamsTemplate() : array
    {
        $params = [
            'daemon'                => ['status' => 'enabled', 'value' => 'on'],
            'pid_file'              => ['status' => 'enabled', 'value' => '/run/motion/motion.pid'],
            // Log settings
            'log_file'              => ['status' => 'enabled', 'value' => '/var/log/motion/motion.log'],
            'log_level'             => ['status' => 'enabled', 'value' => '4'],
            // Target directory for pictures and videos
            'target_dir'            => ['status' => 'enabled', 'value' => '/var/lib/motion'],
            // Motion emulation (for debug purpose)
            'emulate_motion'        => ['status' => 'enabled', 'value' => 'off'],
            'despeckle_filter'      => ['status' => 'enabled', 'value' => 'EedDl'],
            'minimum_motion_frames' => ['status' => 'enabled', 'value' => '1'],
            // Webcontrol settings
            'webcontrol_localhost'  => ['status' => 'enabled', 'value' => 'on'],
            'webcontrol_port'       => ['status' => 'enabled', 'value' => '8082'],
            'webcontrol_parms'      => ['status' => 'enabled', 'value' => ''],
            // Stream server
            'stream_localhost'      => ['status' => 'enabled', 'value' => 'on'],
            'stream_port'           => ['status' => 'enabled', 'value' => '8081'],
            // Pause mode
            'pause'                 => ['status' => 'enabled', 'value' => 'off'],
            // Cameras dir
            'camera_dir'            => ['status' => 'enabled', 'value' => '/var/lib/motionui/cameras/motion/conf-enabled'],
        ];

        return $params;
    }

    /**
     *  Return camera configuration params template
     *  /var/lib/motion/cameras/camera-<id>/camera-<id>.conf
     */
    public static function getCameraParamsTemplate(int $id) : array
    {
        $params = [
            'camera_id' => ['status' => 'enabled', 'value' => $id],
            'camera_name' => ['status' => 'enabled', 'value' => ''],
            'netcam_url' => ['status' => 'enabled', 'value' => 'http://127.0.0.1:1984/api/stream.mjpeg?src=camera_' . $id],
            'netcam_params' => ['status' => 'enabled', 'value' => 'keepalive=on, tolerant_check=on'],
            'width' => ['status' => 'enabled', 'value' => '640'],
            'height' => ['status' => 'enabled', 'value' => '480'],
            'framerate' => ['status' => 'enabled', 'value' => '25'],
            'threshold' => ['status' => 'enabled', 'value' => '500'],
            // Text settings
            'text_left' => ['status' => 'disabled', 'value' => ''],
            'text_right' => ['status' => 'disabled', 'value' => ''],
            'text_scale' => ['status' => 'enabled', 'value' => '2'],
            // Capture settings
            'event_gap' => ['status' => 'enabled', 'value' => '30'],
            'pre_capture' => ['status' => 'enabled', 'value' => '3'],
            'post_capture' => ['status' => 'enabled', 'value' => '4'],
            'target_dir' => ['status' => 'enabled', 'value' => '/var/lib/motion'],
            // Picture settings
            'picture_output' => ['status' => 'disabled', 'value' => 'off'],
            'picture_type' => ['status' => 'enabled', 'value' => 'jpeg'],
            'picture_quality' => ['status' => 'enabled', 'value' => '95'],
            'picture_filename' => ['status' => 'enabled', 'value' => 'camera-' . $id . '/%Y-%m-%d/pictures/%v_%Y-%m-%d_%Hh%Mm%Ss_%q'],
            // Movie settings
            'movie_output' => ['status' => 'enabled', 'value' => 'on'],
            'movie_codec' => ['status' => 'enabled', 'value' => 'mp4'],
            'movie_quality' => ['status' => 'enabled', 'value' => '0'],
            'movie_bps' => ['status' => 'enabled', 'value' => '5000000'],
            'movie_max_time' => ['status' => 'enabled', 'value' => '10'],
            'movie_filename' => ['status' => 'enabled', 'value' => 'camera-' . $id . '/%Y-%m-%d/movies/%v_%Y-%m-%d_%Hh%Mm%Ss_video'],
            // Motion emulation (for debug purpose)
            'emulate_motion' => ['status' => 'disabled', 'value' => 'on'],
            // Event settings
            'on_event_start' => ['status' => 'enabled', 'value' => '/bin/sh /usr/lib/motion/on_event_start %{eventid} %v %t'],
            'on_event_end' => ['status' => 'enabled', 'value' => '/bin/sh /usr/lib/motion/on_event_end %{eventid}'],
            'on_movie_end' => ['status' => 'enabled', 'value' => '/bin/sh /usr/lib/motion/on_event_file %{eventid} %f %w %h %{fps} %D'],
            'on_picture_save' => ['status' => 'enabled', 'value' => '/bin/sh /usr/lib/motion/on_event_file %{eventid} %f %w %h %{fps} %D'],
        ];

        return $params;
    }
}