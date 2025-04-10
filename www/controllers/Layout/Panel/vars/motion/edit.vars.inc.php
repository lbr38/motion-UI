<?php
if (!IS_ADMIN) {
    throw new Exception('You are not allowed to access this panel.');
}

$mycamera = new \Controllers\Camera\Camera();

/**
 *  Check if camera exists
 */
if ($mycamera->existId($item['id']) === false) {
    throw new Exception('Camera does not exist');
}

$id = $item['id'];

$configuration = $mycamera->getConfiguration($id);

try {
    $currentConfiguration = json_decode($configuration['Configuration'], true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    throw new Exception('Error while decoding JSON configuration: ' . $e->getMessage());
}

try {
    $currentMotionConfiguration = json_decode($configuration['Motion_configuration'], true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    throw new Exception('Error while decoding JSON configuration: ' . $e->getMessage());
}

/**
 *  If device ID is not found in current configuration, throw an error
 */
if (empty($currentMotionConfiguration['device_id'])) {
    throw new Exception('Device ID not found in current configuration');
}

unset($configuration);

/**
 *  Define all form parameters
 */
$formParams = [
    'camera-device-settings' => [
        'title' => 'CAMERA DEVICE SETTINGS',
        'icon' => 'camera',
        'params' => [
            'device_id' => [
                'title' => 'DEVICE ID',
                'description' => 'Camera device ID',
                'type' => 'number',
                'required' => true,
                'editable' => false,
                'default' => $id,
                'enabled' => true
            ],
            'device_name' => [
                'title' => 'DEVICE NAME',
                'description' => 'Camera device name',
                'type' => 'text',
                'required' => true,
                'editable' => false,
                'enabled' => true
            ],
            'v4l2_device' => [
                'title' => 'CAMERA DEVICE',
                'description' => 'The v4l2 device to be used for capturing. Example: <code>/dev/video0</code>.',
                'type' => 'text',
            ],
            'v4l2_params' => [
                'title' => 'V4L2 PARAMETERS',
                'description' => 'Comma separated list of configuration parameters (aka controls) for the v4l2 device.',
                'type' => 'text',
            ],
            'netcam_url' => [
                'title' => 'NETWORK CAMERA URL',
                'description' => 'Network camera URL, used for the motion detection. If your camera has a secondary stream with a lower resolution, it is recommended to use the low resolution stream for motion detection and the high resolution stream for recording (see NETWORK CAMERA HIGH RESOLUTION URL).',
                'type' => 'select',
                'options' => [
                    [
                        'value' => $currentConfiguration['main-stream']['device'],
                        'description' => 'Main camera stream'
                    ],
                    [
                        'value' => $currentConfiguration['secondary-stream']['device'],
                        'description' => 'Low resolution camera stream'
                    ],
                    [
                        'value' => 'rtsp://127.0.0.1:8554/camera_' . $id . '?video=all&audio=all',
                        'description' => 'Go2RTC stream'
                    ]
                ]
            ],
            'netcam_params' => [
                'title' => 'NETWORK CAMERA PARAMS',
                'description' => 'Comma separated list of configuration parameters. Format is: option=value,option2=value2.',
                'type' => 'text',
            ],
            'netcam_high_url' => [
                'title' => 'NETWORK CAMERA HIGH RESOLUTION URL',
                'description' => 'Network camera high resolution URL. It is recommended to use the high resolution stream of your camera for recording, and the low resolution stream for motion detection (see NETWORK CAMERA URL).',
                'type' => 'select',
                'options' => [
                    [
                        'value' => $currentConfiguration['main-stream']['device'],
                        'description' => 'Main camera stream'
                    ],
                    [
                        'value' => 'rtsp://127.0.0.1:8554/camera_' . $id . '?video=all&audio=all',
                        'description' => 'Go2RTC stream'
                    ]
                ]
            ],
            'netcam_high_params' => [
                'title' => 'NETWORK CAMERA HIGH RESOLUTION PARAMS',
                'description' => 'Comma separated list of configuration parameters. Format is: option=value,option2=value2.',
                'type' => 'text',
            ],
            'netcam_userpass' => [
                'title' => 'NETWORK CAMERA AUTHENTICATION',
                'description' => 'Username and password for the network camera specified as username:password.',
                'type' => 'text',
            ],
            'width' => [
                'title' => 'WIDTH',
                'description' => 'The width of the image in pixels. Must be a multiple of 8. For better performance, width and height parameters must match the resolution of the camera.',
                'type' => 'number',
                'default' => 640,
            ],
            'height' => [
                'title' => 'HEIGHT',
                'description' => 'The height of the image in pixels. Must be a multiple of 8. For better performance, width and height parameters must match the resolution of the camera.',
                'type' => 'number',
                'default' => 480,
            ],
            'framerate' => [
                'title' => 'FRAME RATE',
                'description' => 'The number of frames to be processed per second for motion detection. For better performance, the framerate parameter must match the framerate of the camera. If you are using a network camera with two streams (low and high resolution), the framerate parameter must match the framerate of the low resolution stream (in fact the stream used for motion detection).',
                'type' => 'range',
                'min' => 2,
                'max' => 100,
                'default' => 15,
            ],
            'rotate' => [
                'title' => 'ROTATE',
                'description' => 'Rotate image the given number of degrees.',
                'type' => 'number',
                'min' => 0,
                'max' => 270,
                'default' => 0
            ],
        ]
    ],
    'motion-detection' => [
        'title' => 'MOTION DETECTION & IMAGE PROCESSING',
        'icon' => 'motion',
        'params' => [
            'emulate_motion' => [
                'title' => 'EMULATE MOTION',
                'description' => 'Always save images even if there was no motion (motion emulation).',
                'type' => 'switch',
                'default' => 'off'
            ],
            'threshold' => [
                'title' => 'MOTION DETECTION SENSITIVITY',
                'description' => 'Minimum number of changed pixels that triggers an event.',
                'type' => 'range',
                'min' => 100,
                'max' => 5000,
                'step' => 100,
                'default' => 1500
            ],
            'threshold_maximum' => [
                'title' => 'THRESHOLD MAXIMUM',
                'description' => 'Maximum number of changed pixels that triggers an event. If the number of changed pixels is over the threshold maximum, no event is triggered. A value of zero disables threshold_maximum.',
                'type' => 'range',
                'min' => 1,
                'max' => 10000,
                'default' => 0
            ],
            'threshold_tune' => [
                'title' => 'THRESHOLD TUNE',
                'description' => 'Continuously adjust the threshold for triggering an event.',
                'type' => 'switch',
                'default' => 'off'
            ],
            'noise_level' => [
                'title' => 'NOISE LEVEL',
                'description' => 'The minimum amount of change in a single pixel before it is counted towards the threshold value.',
                'type' => 'range',
                'min' => 1,
                'max' => 255,
                'default' => 32,
            ],
            'noise_tune' => [
                'title' => 'NOISE TUNE',
                'description' => 'Continuously adjust the noise_level parameter.',
                'type' => 'switch',
                'default' => 'on'
            ],
            'despeckle_filter' => [
                'title' => 'DESPECKLE FILTER',
                'description' => 'Despeckle the image using combinations of (E/e)rode or (D/d)ilate. And ending with optional (l)abeling. This reduces noise in the motion image. Adding a trailing l enables labeling in which only the largest section section is used to calculate the threshold. Typical value is EedDl.',
                'type' => 'text',
                'default' => 'EedDl'
            ],
            'minimum_motion_frames' => [
                'title' => 'MINIMUM MOTION FRAMES',
                'description' => 'The number of frames that must contain motion in order to trigger an event.',
                'type' => 'number',
                'default' => 1
            ],
            'event_gap' => [
                'title' => 'EVENT GAP',
                'description' => 'The seconds of no motion detection that triggers the end of an event.',
                'type' => 'number',
                'default' => 60
            ],
            'pre_capture' => [
                'title' => 'PRE CAPTURE',
                'description' => 'The number of pre-captured (buffered) frames to be captured before an event.',
                'type' => 'number',
                'default' => 0
            ],
            'post_capture' => [
                'title' => 'POST CAPTURE',
                'description' => 'The number of frames to be captured after an event has ended.',
                'type' => 'number',
                'default' => 0
            ],
            'text_left' => [
                'title' => 'TEXT LEFT',
                'description' => 'Text to be written on the left of the image.',
                'type' => 'text',
            ],
            'text_right' => [
                'title' => 'TEXT RIGHT',
                'description' => 'Text to be written on the right of the image.',
                'type' => 'text',
            ],
            'text_changes' => [
                'title' => 'TEXT CHANGES',
                'description' => 'Show the number of pixels that changed in the upper right corner.',
                'type' => 'switch',
                'default' => 'off'
            ],
            'text_scale' => [
                'title' => 'TEXT SCALE',
                'description' => 'Text size.',
                'type' => 'range',
                'default' => 1,
            ],
            'area_detect' => [
                'title' => 'AREA DETECT',
                'description' => 'Detect motion in predefined areas (1 - 9) and when Motion is detected in the area, execute the script. This option is only to execute the on_area_detect script.',
                'type' => 'text',
            ],
            'mask_file' => [
                'title' => 'MASK FILE',
                'description' => 'The full path and filename for the masking pgm file. If needed, the mask will be resized to match the width and height of the image. The areas in the mask file colored black will be excluded from motion detection. Shades of grey on the mask will diminish the detection while areas of white will not have any impact on motion detection.',
                'type' => 'text',
            ],
            'mask_privacy' => [
                'title' => 'MASK PRIVACY',
                'description' => 'The full path and filename for the privacy masking pgm file. This mask completely removes the indicated sections of the image.',
                'type' => 'text',
            ],
            'smart_mask_speed' => [
                'title' => 'SMART MASK SPEED',
                'description' => 'The smartmask is intended to be a dynamic, self-learning mask to decrease sensitivity in areas with frequent motion. The speed specified by this parameter is how quickly the mask gets adjusted. Zero disables the smart mask.',
                'type' => 'range',
                'default' => 0,
                'min' => 0,
                'max' => 10,
            ],
            'lightswitch_percent' => [
                'title' => 'LIGHTSWITCH PERCENT',
                'description' => 'The minimum change in the portion of the image that will trigger a lightswitch condition.',
                'type' => 'range',
                'default' => 0,
                'min' => 0,
                'max' => 100,
            ],
            'lightswitch_frames' => [
                'title' => 'LIGHTSWITCH FRAMES',
                'description' => 'The number of frames to ignore when the lightswitch condition is triggered (see above).',
                'type' => 'number',
                'default' => 5
            ],
            'static_object_time' => [
                'title' => 'STATIC OBJECT TIME',
                'description' => 'Number of seconds before a new object is included in the reference image.',
                'type' => 'number',
                'default' => 0
            ]
        ]
    ],
    'movies-general-settings' => [
        'title' => 'MOVIES GENERAL SETTINGS',
        'icon' => 'video',
        'params' => [
            'movie_output' => [
                'title' => 'MOVIE OUTPUT',
                'description' => 'Encode movies of the motion events.',
                'type' => 'switch',
                'default' => 'on',
                'enabled' => true
            ],
            'movie_output_motion' => [
                'title' => 'MOVIE OUTPUT MOTION',
                'description' => 'Encode movies that show the pixels that changed. If labeling is enabled via the despeckle option, the largest area will be in blue. If smartmask is enabled it will be shown in red.',
                'type' => 'switch',
                'default' => 'off'
            ],
            'movie_max_time' => [
                'title' => 'MOVIE MAX TIME',
                'description' => 'The maximum length of a movie in seconds. Set this to zero for unlimited length.',
                'type' => 'number',
                'default' => 120,
                'enabled' => true
            ],
            'movie_bps' => [
                'title' => 'MOVIE BPS',
                'description' => 'Bitrate to use in encoding of movies. This option is ignored if movie_quality is specified.',
                'type' => 'number',
                'default' => 400000,
            ],
            'movie_quality' => [
                'title' => 'MOVIE QUALITY',
                'description' => 'A value of 0 disables this option while values 1 - 100 change the quality of the movie. The value of 1 means worst quality and 100 is the best quality. High values of this option (e.g. 100) may cause difficulty with some players.',
                'type' => 'range',
                'min' => 1,
                'max' => 100,
                'default' => 60,
                'enabled' => true
            ],
            'movie_container' => [
                'title' => 'MOVIE CONTAINER',
                'description' => 'Container/Codec to be used for the video. Preferred codec can be appended e.g. <code>mkv:libx265</code>',
                'type' => 'text',
                'default' => 'mkv',
                'enabled' => true
            ],
            'movie_retain' => [
                'title' => 'MOVIE RETAIN',
                'description' => 'Retain movie only if the secondary detection occurred.',
                'type' => 'switch',
                'default' => 'off'
            ],
            'movie_passthrough' => [
                'title' => 'MOVIE PASSTHROUGH',
                'description' => 'When using a RTSP, RTMP, mjpeg and some V4l2 cameras, create movie files with the packets obtained directly from the camera.',
                'type' => 'switch',
                'default' => 'off'
            ],
            'movie_filename' => [
                'title' => 'MOVIE FILENAME',
                'description' => 'The file name and optionally the path for the movie relative to target_dir. The file extension is automatically added based upon the container.',
                'type' => 'text',
                'default' => 'camera-' . $id . '/%Y-%m-%d/movies/%v_%Y-%m-%d_%Hh%Mm%Ss_video',
                'editable' => false,
                'enabled' => true
            ],
            'movie_extpipe_use' => [
                'title' => 'MOVIE EXTPIPE USE',
                'description' => 'Specifies whether to send the pictures to pipe for external encoding into a movie.',
                'type' => 'switch',
                'default' => 'off'
            ],
            'movie_extpipe' => [
                'title' => 'MOVIE EXTPIPE',
                'description' => 'The program name and options for processing the images.',
                'type' => 'text'
            ],
            'video_pipe' => [
                'title' => 'VIDEO PIPE',
                'description' => 'The video4linux video loopback device for normal images. The device would be specified in the format like /dev/video1',
                'type' => 'text'
            ],
            'video_pipe_motion' => [
                'title' => 'VIDEO PIPE MOTION',
                'description' => 'The video4linux video loopback device for motion images. The device would be specified in the format like /dev/video1',
                'type' => 'text'
            ]
        ]
    ],
    'pictures-general-settings' => [
        'title' => 'PICTURES GENERAL SETTINGS',
        'icon' => 'picture',
        'params' => [
            'picture_output' => [
                'title' => 'PICTURE OUTPUT',
                'description' => "'on' saves all motion images during an event. 'first' saves only the first image that detected motion. 'best' saves image with most changed pixels.",
                'type' => 'text',
                'default' => 'first',
            ],
            'picture_output_motion' => [
                'title' => 'PICTURE OUTPUT MOTION',
                'description' => 'Save the motion(debug) pictures with the pixels that change as a graytone image. If labeling is enabled via the despeckele option, the largest area will be blue and areas in red are those determined by the smartmask option.',
                'type' => 'switch',
                'default' => 'off'
            ],
            'picture_type' => [
                'title' => 'PICTURE TYPE',
                'description' => 'The type of picture file to output.',
                'type' => 'text',
                'default' => 'jpeg',
                'enabled' => true
            ],
            'picture_quality' => [
                'title' => 'PICTURE QUALITY',
                'description' => 'The image quality for the jpeg or webp images in percent. The value of 1 is worst and 100 is best.',
                'type' => 'range',
                'min' => 5,
                'max' => 100,
                'step' => 5,
                'default' => 75,
                'enabled' => true
            ],
            'picture_exif' => [
                'title' => 'PICTURE EXIF',
                'description' => 'The text for the JPEG EXIF comment with the EXIF timestamp.',
                'type' => 'text',
                'default' => ''
            ],
            'picture_filename' => [
                'title' => 'PICTURE FILENAME',
                'description' => 'The filename for the picture files. The default filename is %v-%Y%m%d%H%M%S-%q.',
                'type' => 'text',
                'default' => 'camera-' . $id . '/%Y-%m-%d/pictures/%v_%Y-%m-%d_%Hh%Mm%Ss_%q',
                'editable' => false,
                'enabled' => true
            ],
        ]
    ],
    'script-execution' => [
        'title' => 'SCRIPT EXECUTION',
        'icon' => 'terminal',
        'params' => [
            'on_event_start' => [
                'title' => 'ON EVENT START',
                'description' => 'The full path and file name of the program/script to be executed at the start of an event.',
                'type' => 'text',
            ],
            'on_event_end' => [
                'title' => 'ON EVENT END',
                'description' => 'The full path and file name of the program/script to be executed at the end of an event.',
                'type' => 'text',
            ],
            'on_picture_save' => [
                'title' => 'ON PICTURE SAVE',
                'description' => 'The full path and file name of the program/script to be executed when a picture is saved.',
                'type' => 'text',
            ],
            'on_motion_detected' => [
                'title' => 'ON MOTION DETECTED',
                'description' => 'The full path and file name of the program/script to be executed when motion is detected.',
                'type' => 'text',
            ],
            'on_area_detected' => [
                'title' => 'ON AREA DETECTED',
                'description' => 'The full path and file name of the program/script to be executed when motion is detected in the area specified.',
                'type' => 'text',
            ],
            'on_movie_start' => [
                'title' => 'ON MOVIE START',
                'description' => 'The full path and file name of the program/script to be executed at the creation of a movie.',
                'type' => 'text',
            ],
            'on_movie_end' => [
                'title' => 'ON MOVIE END',
                'description' => 'The full path and file name of the program/script to be executed when a movie ends.',
                'type' => 'text',
            ],
            'on_camera_lost' => [
                'title' => 'ON CAMERA LOST',
                'description' => 'The full path and file name of the program/script to be executed when the camera is no longer detected.',
                'type' => 'text',
            ],
            'on_camera_found' => [
                'title' => 'ON CAMERA FOUND',
                'description' => 'The full path and file name of the program/script to be executed when a previously lost camera is active again.',
                'type' => 'text',
            ],
            'on_secondary_detect' => [
                'title' => 'ON SECONDARY DETECT',
                'description' => 'The full path and file name of the program/script to be executed when secondary detection occurs.',
                'type' => 'text',
            ],
            'on_action_user' => [
                'title' => 'ON ACTION USER',
                'description' => 'The full path and file name of the program/script to be executed when user selects the user action from the web interface.',
                'type' => 'text',
            ],
            'on_sound_alert' => [
                'title' => 'ON SOUND ALERT',
                'description' => 'The full path and file name of the program/script to be executed when a sound alert is triggered.',
                'type' => 'text',
            ],
        ]
    ],
];
