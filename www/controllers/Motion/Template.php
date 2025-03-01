<?php

namespace Controllers\Motion;

use Exception;

class Template
{
    /**
     *  Return camera configuration params template
     *  Those are the minimal motion params for a camera, more are added by the form in the Motion UI
     *  https://motion-project.github.io/motionplus_config.html
     */
    public function get(int $id) : array
    {
        $params = [
            // General settings
            'device_id' => [
                'value' => '',
                'enabled' => true,
                'editable' => false,
                'locked' => false,
            ],
            'device_name' => [
                'value' => '',
                'enabled' => true,
                'editable' => false,
                'locked' => false,
            ],
            // Camera device settings
            'v4l2_device' => [
                'value' => '',
                'enabled' => false,
                'editable' => true,
                'locked' => false,
            ],
            'v4l2_params' => [
                'value' => '',
                'enabled' => false,
                'editable' => true,
                'locked' => false,
            ],
            'netcam_url' => [
                'value' => '',
                'enabled' => false,
                'editable' => true,
                'locked' => false,
            ],
            'netcam_params' => [
                'value' => '',
                'enabled' => false,
                'editable' => true,
                'locked' => false,
            ],
            'netcam_userpass' => [
                'value' => '',
                'enabled' => false,
                'editable' => true,
                'locked' => false,
            ],
            // Image settings
            'width' => [
                'value' => '',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'height' => [
                'value' => '',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'framerate' => [
                'value' => '',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'rotate' => [
                'value' => '0',
                'enabled' => false,
                'editable' => true,
                'locked' => false,
            ],
            'text_left' => [
                'value' => '',
                'enabled' => false,
                'editable' => true,
                'locked' => false,
            ],
            'text_right' => [
                'value' => '',
                'enabled' => false,
                'editable' => true,
                'locked' => false,
            ],
            'text_scale' => [
                'value' => '2',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            // Motion detection
            'emulate_motion' => [
                'value' => 'on',
                'enabled' => false,
                'editable' => true,
                'locked' => false,
            ],
            'threshold' => [
                'value' => '1500',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'noise_level' => [
                'value' => '32',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'noise_tune' => [
                'value' => 'on',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'despeckle_filter' => [
                'value' => 'EedDl',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'minimum_motion_frames' => [
                'value' => '1',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'event_gap' => [
                'value' => '30',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'pre_capture' => [
                'value' => '1',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'post_capture' => [
                'value' => '5',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            // Picture general info
            'picture_output' => [
                'value' => 'on',
                'enabled' => false,
                'editable' => true,
                'locked' => false,
            ],
            'picture_type' => [
                'value' => 'jpeg',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'picture_quality' => [
                'value' => '95',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'picture_filename' => [
                'value' => 'camera-' . $id . '/%Y-%m-%d/pictures/%v_%Y-%m-%d_%Hh%Mm%Ss_%q',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            // Movie general info
            'movie_output' => [
                'value' => 'on',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'movie_container' => [
                'value' => 'mkv',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'movie_passthrough' => [
                'value' => 'on',
                'enabled' => false,
                'editable' => true,
                'locked' => false,
            ],
            'movie_quality' => [
                'value' => '40',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'movie_max_time' => [
                'value' => '15',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'movie_filename' => [
                'value' => 'camera-' . $id . '/%Y-%m-%d/movies/%v_%Y-%m-%d_%Hh%Mm%Ss_video',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            // Script execution
            'on_event_start' => [
                'value' => '/bin/bash /usr/lib/motion/on_event_start %{eventid} %v %t',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'on_event_end' => [
                'value' => '/bin/bash /usr/lib/motion/on_event_end %{eventid}',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'on_movie_end' => [
                'value' => '/bin/bash /usr/lib/motion/on_event_file %{eventid} %f %w %h %{fps} %D',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ],
            'on_picture_save' => [
                'value' => '/bin/bash /usr/lib/motion/on_event_file %{eventid} %f %w %h %{fps} %D',
                'enabled' => true,
                'editable' => true,
                'locked' => false,
            ]
        ];

        return $params;
    }
}
