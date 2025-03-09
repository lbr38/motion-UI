<div class="flex align-item-center justify-space-between">
    <div>
        <h6 class="margin-top-0">ID</h6>
        <p>#<?= $cameraId ?></p>
    </div>

    <!-- <div class="flex align-item-center column-gap-20 margin-right-20">
        <img src="/assets/icons/camera.svg" class="icon-large" title="Enable/Disable live stream" enabled="<?= $cameraRawParams['stream']['enable'] ?>" />
        <img src="/assets/icons/motion.svg" class="icon-large" title="Enable/Disable motion detection" enabled="<?= $cameraRawParams['motion-detection']['enable'] ?>" />
        <img src="/assets/icons/picture.svg" class="icon-large" title="Enable/Disable timelapse" enabled="<?= $cameraRawParams['timelapse']['enable'] ?>" />
    </div> -->
</div>

<form id="edit-global-settings-form" camera-id="<?= $cameraId ?>" autocomplete="off">
    <h6 class="required">NAME</h6>
    <input type="text" class="form-param" param-name="name" value="<?= $cameraRawParams['name'] ?>" />

    <h6 class="required">CAMERA MAIN STREAM</h6>
    <p class="note">Device path like /dev/video0 or URL like http://... or rtsp://... are supported.</p>
    <input type="text" class="form-param" param-name="main-stream-device" value="<?= $cameraRawParams['main-stream']['device'] ?>" placeholder="e.g. /dev/video0 or http(s)://... or rtsp://..." />

    <h6 class="required">RESOLUTION</h6>
    <p class="note">The selected resolution must match the resolution of the camera.</p>
    <select class="form-param" param-name="main-stream-resolution">
        <!-- 16/9 -->
        <option disabled>16/9 resolutions:</option>
        <option value="640x360" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '640x360') ? 'selected' : ''; ?>>640x360 (360p)</option>
        <option value="854x480" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '854x480') ? 'selected' : ''; ?>>854x480 (480p)</option>
        <option value="960x540" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '960x540') ? 'selected' : ''; ?>>960x540 (540p)</option>
        <option value="1024x576" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '1024x576') ? 'selected' : ''; ?>>1024x576 (576p)</option>
        <option value="1280x720" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '1280x720') ? 'selected' : ''; ?>>1280x720 (720p)</option>
        <option value="1920x1080" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '1920x1080') ? 'selected' : ''; ?>>1920x1080 (1080p)</option>
        <option value="2560x1440" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '2560x1440') ? 'selected' : ''; ?>>2560x1440 (1440p)</option>
        <option value="3840x2160" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '3840x2160') ? 'selected' : ''; ?>>3840x2160 (2160p)</option>
        <option value="5120x2880" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '5120x2880') ? 'selected' : ''; ?>>5120x2880 (2880p)</option>
        <option value="7680x4320" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '7680x4320') ? 'selected' : ''; ?>>7680x4320 (4320p)</option>
        <!-- 4/3 -->
        <option disabled>4/3 resolutions:</option>
        <option value="640x480" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '640x480') ? 'selected' : ''; ?>>640x480</option>
        <option value="800x600" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '800x600') ? 'selected' : ''; ?>>800x600</option>
        <option value="960x720" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '960x720') ? 'selected' : ''; ?>>960x720</option>
        <option value="1024x768" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '1024x768') ? 'selected' : ''; ?>>1024x768</option>
        <option value="1152x864" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '1152x864') ? 'selected' : ''; ?>>1152x864</option>
        <option value="1280x960" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '1280x960') ? 'selected' : ''; ?>>1280x960</option>
        <option value="1400x1050" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '1400x1050') ? 'selected' : ''; ?>>1400x1050</option>
        <option value="1440x1080" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '1440x1080') ? 'selected' : ''; ?>>1440x1080</option>
        <option value="1600x1200" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '1600x1200') ? 'selected' : ''; ?>>1600x1200</option>
        <option value="1856x1392" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '1856x1392') ? 'selected' : ''; ?>>1856x1392</option>
        <option value="1920x1440" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '1920x1440') ? 'selected' : ''; ?>>1920x1440</option>
        <option value="2048x1536" <?php echo ($cameraRawParams['main-stream']['width'] . 'x' . $cameraRawParams['main-stream']['height'] == '2048x1536') ? 'selected' : ''; ?>>2048x1536</option>
    </select>

    <h6>FRAME RATE</h6>
    <p class="note">The specified frame rate must match the frame rate of the camera.</p>
    <input type="number" class="form-param" param-name="main-stream-framerate" value="<?= $cameraRawParams['main-stream']['framerate'] ?>" min="2" />

    <h6>ROTATE</h6>
    <p class="note">Set to 0 to disable rotation. Warning: rotating the camera feed is a CPU intensive operation.</p>
    <select class="form-param" param-name="main-stream-rotate">
        <option value="0" <?php echo $cameraRawParams['main-stream']['rotate'] == "0" ? 'selected' : '' ?>>0</option>
        <option value="90" <?php echo $cameraRawParams['main-stream']['rotate'] == "90" ? 'selected' : '' ?>>90</option>
        <option value="180" <?php echo $cameraRawParams['main-stream']['rotate'] == "180" ? 'selected' : '' ?>>180</option>
        <option value="270" <?php echo $cameraRawParams['main-stream']['rotate'] == "270" ? 'selected' : '' ?>>270</option>
    </select>

    <h6>TEXT LEFT</h6>
    <p class="note">Text to display on the left side of the camera feed.</p>
    <input type="text" class="form-param" param-name="main-stream-text-left" value="<?= $cameraRawParams['main-stream']['text-left'] ?>" />

    <h6>TEXT RIGHT</h6>
    <p class="note">Text to display on the right side of the camera feed.</p>
    <input type="text" class="form-param" param-name="main-stream-text-right" value="<?= $cameraRawParams['main-stream']['text-right'] ?>" />  
    
    <h6>TIMESTAMP LEFT</h6>
    <p class="note">Display timestamp on the left side of the camera feed.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="main-stream-timestamp-left" <?php echo $cameraRawParams['main-stream']['timestamp-left'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6>TIMESTAMP RIGHT</h6>
    <p class="note">Display timestamp on the right side of the camera feed.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="main-stream-timestamp-right" <?php echo $cameraRawParams['main-stream']['timestamp-right'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <hr class="margin-top-20 margin-bottom-20">

    <h6>CAMERA SECONDARY STREAM</h6>
    <p class="note">If your camera supports multiple streams and provides a secondary stream with a lower resolution, you can add it here. This secondary stream will be used for the motion detection.</p>
    <p class="note">Device URL like http://... or rtsp://... are supported.</p>
    <input type="text" class="form-param" param-name="secondary-stream-device" value="<?= $cameraRawParams['secondary-stream']['device'] ?>" placeholder="e.g. http(s)://... or rtsp://..." />

    <h6>SECONDARY STREAM RESOLUTION</h6>
    <p class="note">The selected resolution must match the resolution of the secondary stream of the camera.</p>
    <select class="form-param" param-name="secondary-stream-resolution">
        <!-- 16/9 -->
        <option disabled>16/9 resolutions:</option>
        <option value="640x360" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '640x360') ? 'selected' : ''; ?>>640x360 (360p)</option>
        <option value="854x480" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '854x480') ? 'selected' : ''; ?>>854x480 (480p)</option>
        <option value="960x540" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '960x540') ? 'selected' : ''; ?>>960x540 (540p)</option>
        <option value="1024x576" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '1024x576') ? 'selected' : ''; ?>>1024x576 (576p)</option>
        <option value="1280x720" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '1280x720') ? 'selected' : ''; ?>>1280x720 (720p)</option>
        <option value="1920x1080" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '1920x1080') ? 'selected' : ''; ?>>1920x1080 (1080p)</option>
        <option value="2560x1440" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '2560x1440') ? 'selected' : ''; ?>>2560x1440 (1440p)</option>
        <option value="3840x2160" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '3840x2160') ? 'selected' : ''; ?>>3840x2160 (2160p)</option>
        <option value="5120x2880" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '5120x2880') ? 'selected' : ''; ?>>5120x2880 (2880p)</option>
        <option value="7680x4320" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '7680x4320') ? 'selected' : ''; ?>>7680x4320 (4320p)</option>
        <!-- 4/3 -->
        <option disabled>4/3 resolutions:</option>
        <option value="640x480" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '640x480') ? 'selected' : ''; ?>>640x480</option>
        <option value="800x600" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '800x600') ? 'selected' : ''; ?>>800x600</option>
        <option value="960x720" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '960x720') ? 'selected' : ''; ?>>960x720</option>
        <option value="1024x768" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '1024x768') ? 'selected' : ''; ?>>1024x768</option>
        <option value="1152x864" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '1152x864') ? 'selected' : ''; ?>>1152x864</option>
        <option value="1280x960" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '1280x960') ? 'selected' : ''; ?>>1280x960</option>
        <option value="1400x1050" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '1400x1050') ? 'selected' : ''; ?>>1400x1050</option>
        <option value="1440x1080" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '1440x1080') ? 'selected' : ''; ?>>1440x1080</option>
        <option value="1600x1200" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '1600x1200') ? 'selected' : ''; ?>>1600x1200</option>
        <option value="1856x1392" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '1856x1392') ? 'selected' : ''; ?>>1856x1392</option>
        <option value="1920x1440" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '1920x1440') ? 'selected' : ''; ?>>1920x1440</option>
        <option value="2048x1536" <?php echo ($cameraRawParams['secondary-stream']['width'] . 'x' . $cameraRawParams['secondary-stream']['height'] == '2048x1536') ? 'selected' : ''; ?>>2048x1536</option>
    </select>

    <h6>SECONDARY STREAM FRAMERATE</h6>
    <p class="note">Specified frame rate must match the frame rate of the secondary stream of the camera.</p>
    <input type="number" class="form-param" param-name="secondary-stream-framerate" value="<?= $cameraRawParams['secondary-stream']['framerate'] ?>" min="2" />

    <hr class="margin-top-20 margin-bottom-20">

    <h6>AUTHENTICATION</h6>
    <p class="note">If your camera requires authentication (to access the video stream or ONVIF service).</p>
    <label class="onoff-switch-label">
        <input class="onoff-switch-input" type="checkbox" <?php echo (!empty($cameraRawParams['authentication']['username']) || !empty($cameraRawParams['authentication']['password'])) ? 'checked' : ''; ?>>
        <span class="onoff-switch-slider toggle-btn" target=".basic-auth-fields"></span>
    </label>

    <div class="basic-auth-fields <?php echo (empty($cameraRawParams['authentication']['username']) && empty($cameraRawParams['authentication']['password'])) ? 'hide' : ''; ?>">
        <h6>USERNAME</h6>
        <input type="text" class="form-param" param-name="username" value="<?= $cameraRawParams['authentication']['username'] ?>" />

        <h6>PASSWORD</h6>
        <input type="password" class="form-param" param-name="password" value="<?= $cameraRawParams['authentication']['password'] ?>" />
    </div>

    <hr class="margin-top-20 margin-bottom-20">

    <h6>ONVIF ENABLED</h6>
    <p class="note">If camera supports ONVIF protocol. This will allow you to move the camera using the PTZ controls.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="onvif-enable" <?php echo ($cameraRawParams['onvif']['enable'] == 'true') ? 'checked' : ''; ?> >
        <span class="onoff-switch-slider"></span>
    </label>

    <div id="onvif-fields" class="<?= $onvifFieldsClass ?>">
        <h6>ONVIF PORT</h6>
        <p class="note">Port number of the ONVIF service. Default is 80.</p>
        <input type="number" class="form-param" param-name="onvif-port" value="<?= $cameraRawParams['onvif']['port'] ?>" />

        <!-- <h6>ONVIF URI</h6>
        <input type="text" class="form-param" param-name="onvif-uri" value="<?= $cameraRawParams['onvif']['uri'] ?>" placeholder="e.g. /onvif/device_service" /> -->

        <?php
        if (!empty($cameraRawParams['onvif']['url'])) {
            echo '<p class="note">Target URL: ' . $cameraRawParams['onvif']['url'] . '</p>';
        } ?>
    </div>

    <!-- <h6>HARDWARE ACCELERATION</h6>
    <p class="note">Enable hardware acceleration for decoding and encoding video streams.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="hardware-acceleration" <?php echo $cameraRawParams['hardware-acceleration'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label> -->

    <hr class="margin-top-20 margin-bottom-20">

    <h6>ENABLE MOTION DETECTION</h6>
    <p class="note">Enable motion detection for this camera.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="motion-detection-enable" <?php echo $cameraRawParams['motion-detection']['enable'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6>CONFIGURE MOTION DETECTION</h6>
    <p class="note">Configure motion detection for this camera.</p>
    <button type="button" class="btn-medium-tr get-motion-config-form-btn margin-top-5" camera-id="<?= $cameraId ?>">Configure</button>

    <hr class="margin-top-20 margin-bottom-20">

    <h6>DISPLAY LIVE STREAM</h6>
    <p class="note">Display camera output on the live stream page.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="stream-enable" <?php echo $cameraRawParams['stream']['enable'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6>STREAM TECHNOLOGY</h6>
    <p class="note">MSE should work without any additional configuration.</p>
    <p class="note">WebRTC is more efficient and offer less latency but may not work in some cases.</p>
    <p class="note">MJPEG should be used with http:// cameras.</p>

    <select class="form-param" param-name="stream-technology">
        <option value="mse" <?php echo ($cameraRawParams['stream']['technology'] === 'mse') ? 'selected' : '' ?>>MSE</option>
        <option value="webrtc" <?php echo ($cameraRawParams['stream']['technology'] === 'webrtc') ? 'selected' : '' ?>>WebRTC</option>
        <option value="mjpeg" <?php echo ($cameraRawParams['stream']['technology'] === 'mjpeg') ? 'selected' : '' ?>>MJPEG</option>
    </select>

    <hr class="margin-top-20 margin-bottom-20">

    <h6>ENABLE TIMELAPSE</h6>
    <p class="note">Enable timelapse for this camera.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="timelapse-enable" <?php echo $cameraRawParams['timelapse']['enable'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <br><br>
    <div class="flex column-gap-10">
        <button type="submit" class="btn-small-green">Save</button>
        <button type="button" class="btn-small-red delete-camera-btn" title="Delete camera" camera-id="<?= $cameraId ?>">Delete</button>
    </div>
</form>

<br>
<br>