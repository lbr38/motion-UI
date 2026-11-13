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
        'title' => __('CAMERA DEVICE SETTINGS'),
        'icon' => 'camera',
        'params' => [
            'device_id' => [
                'title' => __('DEVICE ID'),
                'description' => __('Camera device ID'),
                'type' => 'number',
                'required' => true,
                'editable' => false,
                'default' => $id,
                'enabled' => true
            ],
            'device_name' => [
                'title' => __('DEVICE NAME'),
                'description' => __('Camera device name'),
                'type' => 'text',
                'required' => true,
                'editable' => false,
                'enabled' => true
            ],
            'v4l2_device' => [
                'title' => __('CAMERA DEVICE'),
                'description' => __('The v4l2 device to be used for capturing. Example: <code>/dev/video0</code>.'),
                'type' => 'text',
            ],
            'v4l2_params' => [
                'title' => __('V4L2 PARAMETERS'),
                'description' => __('Comma separated list of configuration parameters (aka controls) for the v4l2 device.'),
                'type' => 'text',
            ],
            'netcam_url' => [
                'title' => __('NETWORK CAMERA URL'),
                'description' => __('Network camera URL, used for the motion detection. If your camera has a secondary stream with a lower resolution, it is recommended to use the low resolution stream for motion detection and the high resolution stream for recording (see NETWORK CAMERA HIGH RESOLUTION URL).'),
                'type' => 'select',
                'options' => [
                    [
                        'value' => $currentConfiguration['main-stream']['device'],
                        'description' => __('Main camera stream')
                    ],
                    [
                        'value' => $currentConfiguration['secondary-stream']['device'],
                        'description' => __('Low resolution camera stream')
                    ],
                    [
                        'value' => 'rtsp://127.0.0.1:8554/camera_' . $id . '?video=all&audio=all',
                        'description' => __('Go2RTC stream')
                    ]
                ]
            ],
            'netcam_params' => [
                'title' => __('NETWORK CAMERA PARAMS'),
                'description' => __('Comma separated list of configuration parameters. Format is: option=value,option2=value2.'),
                'type' => 'text',
            ],
            'netcam_high_url' => [
                'title' => __('NETWORK CAMERA HIGH RESOLUTION URL'),
                'description' => __('Network camera high resolution URL. It is recommended to use the high resolution stream of your camera for recording, and the low resolution stream for motion detection (see NETWORK CAMERA URL).'),
                'type' => 'select',
                'options' => [
                    [
                        'value' => $currentConfiguration['main-stream']['device'],
                        'description' => __('Main camera stream')
                    ],
                    [
                        'value' => 'rtsp://127.0.0.1:8554/camera_' . $id . '?video=all&audio=all',
                        'description' => __('Go2RTC stream')
                    ]
                ]
            ],
            'netcam_high_params' => [
                'title' => __('NETWORK CAMERA HIGH RESOLUTION PARAMS'),
                'description' => __('Comma separated list of configuration parameters. Format is: option=value,option2=value2.'),
                'type' => 'text',
            ],
            'netcam_userpass' => [
                'title' => __('NETWORK CAMERA AUTHENTICATION'),
                'description' => __('Username and password for the network camera specified as username:password.'),
                'type' => 'text',
            ],
            'width' => [
                'title' => __('WIDTH'),
                'description' => __('The width of the image in pixels. Must be a multiple of 8. For better performance, width and height parameters must match the resolution of the camera.'),
                'type' => 'number',
                'default' => 640,
            ],
            'height' => [
                'title' => __('HEIGHT'),
                'description' => __('The height of the image in pixels. Must be a multiple of 8. For better performance, width and height parameters must match the resolution of the camera.'),
                'type' => 'number',
                'default' => 480,
            ],
            'framerate' => [
                'title' => __('FRAME RATE'),
                'description' => __('The number of frames to be processed per second for motion detection. For better performance, the framerate parameter must match the framerate of the camera. If you are using a network camera with two streams (low and high resolution), the framerate parameter must match the framerate of the low resolution stream (in fact the stream used for motion detection).'),
                'type' => 'range',
                'min' => 2,
                'max' => 100,
                'default' => 15,
            ],
            'rotate' => [
                'title' => __('ROTATE'),
                'description' => __('Rotate image the given number of degrees.'),
                'type' => 'number',
                'min' => 0,
                'max' => 270,
                'default' => 0
            ],
            'device_tmo' => [
                'title' => __('DEVICE TIMEOUT'),
                'description' => __('Number of seconds that elapse before the device is deemed to be lost.'),
                'type' => 'number',
                'default' => 30
            ],
            'watchdog_tmo' => [
                'title' => __('WATCHDOG TIMEOUT'),
                'description' => __('Number of seconds that elapse before a camera is deemed to be unresponsive and Motion triggers a watchdog timeout and begins to force the camera to shut down.'),
                'type' => 'number',
                'default' => 30
            ],
            'watchdog_kill' => [
                'title' => __('WATCHDOG KILL'),
                'description' => __('Number of seconds that elapse after the watchdog_tmo that Motion waits until the kill processes begin to force the camera to shut down.'),
                'type' => 'number',
                'default' => 10
            ]
        ]
    ],
    'motion-detection' => [
        'title' => __('MOTION DETECTION & IMAGE PROCESSING'),
        'icon' => 'motion',
        'params' => [
            'emulate_motion' => [
                'title' => __('EMULATE MOTION DETECTION'),
                'description' => __('Always save images even if there was no motion (motion emulation).'),
                'type' => 'switch',
                'default' => 'off'
            ],
            'pause' => [
                'title' => __('PAUSE MOTION DETECTION'),
                'description' => __('Pause detection at start up.'),
                'type' => 'switch',
                'default' => 'off'
            ],
            'threshold' => [
                'title' => __('MOTION DETECTION SENSITIVITY'),
                'description' => __('Minimum number of changed pixels that triggers an event.'),
                'type' => 'range',
                'min' => 100,
                'max' => 5000,
                'step' => 100,
                'default' => 1500
            ],
            'threshold_maximum' => [
                'title' => __('THRESHOLD MAXIMUM'),
                'description' => __('Maximum number of changed pixels that triggers an event. If the number of changed pixels is over the threshold maximum, no event is triggered. A value of zero disables threshold_maximum.'),
                'type' => 'range',
                'min' => 1,
                'max' => 10000,
                'default' => 0
            ],
            'threshold_tune' => [
                'title' => __('THRESHOLD TUNE'),
                'description' => __('Continuously adjust the threshold for triggering an event.'),
                'type' => 'switch',
                'default' => 'off'
            ],
            'threshold_sdevx' => [
                'title' => __('THRESHOLD SDEVX'),
                'description' => __('The maximum standard deviation of the changed pixels in the x (width) axis.'),
                'type' => 'number',
                'min' => 0,
                'default' => 0
            ],
            'threshold_sdevy' => [
                'title' => __('THRESHOLD SDEVY'),
                'description' => __('The maximum standard deviation of the changed pixels in the y (height) axis.'),
                'type' => 'number',
                'min' => 0,
                'default' => 0
            ],
            'threshold_sdevxy' => [
                'title' => __('THRESHOLD SDEVXY'),
                'description' => __('The maximum standard deviation of the changed pixels in the x (width) and y(height) axis.'),
                'type' => 'number',
                'min' => 0,
                'default' => 0
            ],
            'threshold_ratio' => [
                'title' => __('THRESHOLD RATIO'),
                'description' => __('The maximum ratio comparing the pixels that went white to black and vice versa.'),
                'type' => 'number',
                'min' => 0,
                'default' => 0
            ],
            'threshold_ratio_change' => [
                'title' => __('THRESHOLD RATIO CHANGE'),
                'description' => __('The minimum change in the ratio to trigger a event.'),
                'type' => 'number',
                'min' => 0,
                'default' => 0
            ],
            'noise_level' => [
                'title' => __('NOISE LEVEL'),
                'description' => __('The minimum amount of change in a single pixel before it is counted towards the threshold value.'),
                'type' => 'range',
                'min' => 1,
                'max' => 255,
                'default' => 32,
            ],
            'noise_tune' => [
                'title' => __('NOISE TUNE'),
                'description' => __('Continuously adjust the noise_level parameter.'),
                'type' => 'switch',
                'default' => 'on'
            ],
            'despeckle_filter' => [
                'title' => __('DESPECKLE FILTER'),
                'description' => __('Despeckle the image using combinations of (E/e)rode or (D/d)ilate. And ending with optional (l)abeling. This reduces noise in the motion image. Adding a trailing l enables labeling in which only the largest section section is used to calculate the threshold. Typical value is EedDl.'),
                'type' => 'text',
                'default' => 'EedDl'
            ],
            'minimum_motion_frames' => [
                'title' => __('MINIMUM MOTION FRAMES'),
                'description' => __('The number of frames that must contain motion in order to trigger an event.'),
                'type' => 'number',
                'default' => 1
            ],
            'event_gap' => [
                'title' => __('EVENT GAP'),
                'description' => __('The seconds of no motion detection that triggers the end of an event.'),
                'type' => 'number',
                'default' => 60
            ],
            'pre_capture' => [
                'title' => __('PRE CAPTURE'),
                'description' => __('The number of pre-captured (buffered) frames to be captured before an event.'),
                'type' => 'number',
                'default' => 0
            ],
            'post_capture' => [
                'title' => __('POST CAPTURE'),
                'description' => __('The number of frames to be captured after an event has ended.'),
                'type' => 'number',
                'default' => 0
            ],
            'text_left' => [
                'title' => __('TEXT LEFT'),
                'description' => __('Text to be written on the left of the image.'),
                'type' => 'text',
            ],
            'text_right' => [
                'title' => __('TEXT RIGHT'),
                'description' => __('Text to be written on the right of the image.'),
                'type' => 'text',
            ],
            'text_changes' => [
                'title' => __('TEXT CHANGES'),
                'description' => __('Show the number of pixels that changed in the upper right corner.'),
                'type' => 'switch',
                'default' => 'off'
            ],
            'text_scale' => [
                'title' => __('TEXT SCALE'),
                'description' => __('Text size.'),
                'type' => 'range',
                'default' => 1,
            ],
            'area_detect' => [
                'title' => __('AREA DETECT'),
                'description' => __('Detect motion in predefined areas (1 - 9) and when Motion is detected in the area, execute the script. This option is only to execute the on_area_detect script.'),
                'type' => 'text',
            ],
            'mask_file' => [
                'title' => __('MASK FILE'),
                'description' => __('The full path and filename for the masking pgm file. If needed, the mask will be resized to match the width and height of the image. The areas in the mask file colored black will be excluded from motion detection. Shades of grey on the mask will diminish the detection while areas of white will not have any impact on motion detection.'),
                'type' => 'text',
            ],
            'mask_privacy' => [
                'title' => __('MASK PRIVACY'),
                'description' => __('The full path and filename for the privacy masking pgm file. This mask completely removes the indicated sections of the image.'),
                'type' => 'text',
            ],
            'smart_mask_speed' => [
                'title' => __('SMART MASK SPEED'),
                'description' => __('The smartmask is intended to be a dynamic, self-learning mask to decrease sensitivity in areas with frequent motion. The speed specified by this parameter is how quickly the mask gets adjusted. Zero disables the smart mask.'),
                'type' => 'range',
                'default' => 0,
                'min' => 0,
                'max' => 10,
            ],
            'locate_motion_mode' => [
                'title' => __('LOCATE MOTION MODE'),
                'description' => __('Locate and draw around/on the moving object. Preview only applies to pictures.'),
                'type' => 'select',
                'options' => [
                    [
                        'value' => 'on',
                    ],
                    [
                        'value' => 'off',
                    ],
                    [
                        'value' => 'preview',
                    ]
                ],
                'default' => 'off'
            ],
            'locate_motion_style' => [
                'title' => __('LOCATE MOTION STYLE'),
                'description' => __('Set the look and style of the locate mode.'),
                'type' => 'select',
                'options' => [
                    [
                        'value' => 'box',
                    ],
                    [
                        'value' => 'redbox',
                    ],
                    [
                        'value' => 'cross',
                    ],
                    [
                        'value' => 'redcross',
                    ]
                ],
                'default' => 'box'
            ],
            'lightswitch_percent' => [
                'title' => __('LIGHTSWITCH PERCENT'),
                'description' => __('The minimum change in the portion of the image that will trigger a lightswitch condition.'),
                'type' => 'range',
                'default' => 0,
                'min' => 0,
                'max' => 100,
            ],
            'lightswitch_frames' => [
                'title' => __('LIGHTSWITCH FRAMES'),
                'description' => __('The number of frames to ignore when the lightswitch condition is triggered (see above).'),
                'type' => 'number',
                'default' => 5
            ],
            'static_object_time' => [
                'title' => __('STATIC OBJECT TIME'),
                'description' => __('Number of seconds before a new object is included in the reference image.'),
                'type' => 'number',
                'default' => 0
            ]
        ]
    ],
    'movies-general-settings' => [
        'title' => __('MOVIES GENERAL SETTINGS'),
        'icon' => 'video',
        'params' => [
            'movie_output' => [
                'title' => __('MOVIE OUTPUT'),
                'description' => __('Encode movies of the motion events.'),
                'type' => 'switch',
                'default' => 'on',
                'enabled' => true
            ],
            'movie_output_motion' => [
                'title' => __('MOVIE OUTPUT MOTION'),
                'description' => __('Encode movies that show the pixels that changed. If labeling is enabled via the despeckle option, the largest area will be in blue. If smartmask is enabled it will be shown in red.'),
                'type' => 'switch',
                'default' => 'off'
            ],
            'movie_max_time' => [
                'title' => __('MOVIE MAX TIME'),
                'description' => __('The maximum length of a movie in seconds. Set this to zero for unlimited length.'),
                'type' => 'number',
                'default' => 120,
                'enabled' => true
            ],
            'movie_bps' => [
                'title' => __('MOVIE BPS'),
                'description' => __('Bitrate to use in encoding of movies. This option is ignored if movie_quality is specified.'),
                'type' => 'number',
                'default' => 400000,
            ],
            'movie_quality' => [
                'title' => __('MOVIE QUALITY'),
                'description' => __('A value of 0 disables this option while values 1 - 100 change the quality of the movie. The value of 1 means worst quality and 100 is the best quality. High values of this option (e.g. 100) may cause difficulty with some players.'),
                'type' => 'range',
                'min' => 1,
                'max' => 100,
                'default' => 60,
                'enabled' => true
            ],
            'movie_container' => [
                'title' => __('MOVIE CONTAINER'),
                'description' => __('Container/Codec to be used for the video. Preferred codec can be appended e.g. <code>mkv:libx265</code>'),
                'type' => 'text',
                'default' => 'mkv',
                'enabled' => true
            ],
            'movie_retain' => [
                'title' => __('MOVIE RETAIN'),
                'description' => __('Retain movie only if the secondary detection occurred.'),
                'type' => 'switch',
                'default' => 'off'
            ],
            'movie_passthrough' => [
                'title' => __('MOVIE PASSTHROUGH'),
                'description' => __('When using a RTSP, RTMP, mjpeg and some V4l2 cameras, create movie files with the packets obtained directly from the camera.'),
                'type' => 'switch',
                'default' => 'off'
            ],
            'movie_filename' => [
                'title' => __('MOVIE FILENAME'),
                'description' => __('The file name and optionally the path for the movie relative to target_dir. The file extension is automatically added based upon the container.'),
                'type' => 'text',
                'default' => 'camera-' . $id . '/%Y-%m-%d/movies/%v_%Y-%m-%d_%Hh%Mm%Ss_video',
                'enabled' => true
            ],
            'movie_extpipe_use' => [
                'title' => __('MOVIE EXTPIPE USE'),
                'description' => __('Specifies whether to send the pictures to pipe for external encoding into a movie.'),
                'type' => 'switch',
                'default' => 'off'
            ],
            'movie_extpipe' => [
                'title' => __('MOVIE EXTPIPE'),
                'description' => __('The program name and options for processing the images.'),
                'type' => 'text'
            ],
            'video_pipe' => [
                'title' => __('VIDEO PIPE'),
                'description' => __('The video4linux video loopback device for normal images. The device would be specified in the format like /dev/video1'),
                'type' => 'text'
            ],
            'video_pipe_motion' => [
                'title' => __('VIDEO PIPE MOTION'),
                'description' => __('The video4linux video loopback device for motion images. The device would be specified in the format like /dev/video1'),
                'type' => 'text'
            ]
        ]
    ],
    'pictures-general-settings' => [
        'title' => __('PICTURES GENERAL SETTINGS'),
        'icon' => 'picture',
        'params' => [
            'picture_output' => [
                'title' => __('PICTURE OUTPUT'),
                'description' => __('\'on\' saves all motion images during an event. \'first\' saves only the first image that detected motion. \'best\' saves image with most changed pixels.'),
                'type' => 'text',
                'default' => 'first',
            ],
            'picture_output_motion' => [
                'title' => __('PICTURE OUTPUT MOTION'),
                'description' => __('Save the motion(debug) pictures with the pixels that change as a graytone image. If labeling is enabled via the despeckele option, the largest area will be blue and areas in red are those determined by the smartmask option.'),
                'type' => 'switch',
                'default' => 'off'
            ],
            'picture_type' => [
                'title' => __('PICTURE TYPE'),
                'description' => __('The type of picture file to output.'),
                'type' => 'text',
                'default' => 'jpeg',
                'enabled' => true
            ],
            'picture_quality' => [
                'title' => __('PICTURE QUALITY'),
                'description' => __('The image quality for the jpeg or webp images in percent. The value of 1 is worst and 100 is best.'),
                'type' => 'range',
                'min' => 5,
                'max' => 100,
                'step' => 5,
                'default' => 75,
                'enabled' => true
            ],
            'picture_exif' => [
                'title' => __('PICTURE EXIF'),
                'description' => __('The text for the JPEG EXIF comment with the EXIF timestamp.'),
                'type' => 'text',
                'default' => ''
            ],
            'picture_filename' => [
                'title' => __('PICTURE FILENAME'),
                'description' => __('The filename for the picture files. The default filename is %v-%Y%m%d%H%M%S-%q.'),
                'type' => 'text',
                'default' => 'camera-' . $id . '/%Y-%m-%d/pictures/%v_%Y-%m-%d_%Hh%Mm%Ss_%q',
                'enabled' => true
            ],
        ]
    ],
    'script-execution' => [
        'title' => __('SCRIPT EXECUTION'),
        'icon' => 'terminal',
        'params' => [
            'on_event_start' => [
                'title' => __('ON EVENT START'),
                'description' => __('The full path and file name of the program/script to be executed at the start of an event.'),
                'type' => 'text',
            ],
            'on_event_end' => [
                'title' => __('ON EVENT END'),
                'description' => __('The full path and file name of the program/script to be executed at the end of an event.'),
                'type' => 'text',
            ],
            'on_picture_save' => [
                'title' => __('ON PICTURE SAVE'),
                'description' => __('The full path and file name of the program/script to be executed when a picture is saved.'),
                'type' => 'text',
            ],
            'on_motion_detected' => [
                'title' => __('ON MOTION DETECTED'),
                'description' => __('The full path and file name of the program/script to be executed when motion is detected.'),
                'type' => 'text',
            ],
            'on_area_detected' => [
                'title' => __('ON AREA DETECTED'),
                'description' => __('The full path and file name of the program/script to be executed when motion is detected in the area specified.'),
                'type' => 'text',
            ],
            'on_movie_start' => [
                'title' => __('ON MOVIE START'),
                'description' => __('The full path and file name of the program/script to be executed at the creation of a movie.'),
                'type' => 'text',
            ],
            'on_movie_end' => [
                'title' => __('ON MOVIE END'),
                'description' => __('The full path and file name of the program/script to be executed when a movie ends.'),
                'type' => 'text',
            ],
            'on_camera_lost' => [
                'title' => __('ON CAMERA LOST'),
                'description' => __('The full path and file name of the program/script to be executed when the camera is no longer detected.'),
                'type' => 'text',
            ],
            'on_camera_found' => [
                'title' => __('ON CAMERA FOUND'),
                'description' => __('The full path and file name of the program/script to be executed when a previously lost camera is active again.'),
                'type' => 'text',
            ],
            'on_secondary_detect' => [
                'title' => __('ON SECONDARY DETECT'),
                'description' => __('The full path and file name of the program/script to be executed when secondary detection occurs.'),
                'type' => 'text',
            ],
            'on_sound_alert' => [
                'title' => __('ON SOUND ALERT'),
                'description' => __('The full path and file name of the program/script to be executed when a sound alert is triggered.'),
                'type' => 'text',
            ],
        ]
    ],
];
