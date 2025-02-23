<h6>ID</h6>
<p>#<?= $cameraId ?></p>

<form id="edit-global-settings-form" camera-id="<?= $cameraId ?>" autocomplete="off">
    <h6 class="required">NAME</h6>
    <input type="text" class="form-param" param-name="name" value="<?= $cameraRawParams['name'] ?>" />
        
    <h6 class="required">DEVICE or URL</h6>
    <p class="note">Device path like /dev/video0 or URL like http://... or rtsp://... are supported.</p>
    <input type="text" class="form-param" param-name="url" value="<?= $cameraRawParams['url'] ?>" placeholder="e.g. /dev/video0 or http(s)://... or rtsp://..." />

    <h6 class="required">RESOLUTION</h6>
    <p class="note">The selected resolution should match the resolution of the camera.</p>
    <select class="form-param" param-name="resolution">
        <!-- 4/3 -->
        <option disabled>4/3 resolutions:</option>
        <option value="640x480" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '640x480') ? 'selected' : ''; ?>>640x480</option>
        <option value="800x600" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '800x600') ? 'selected' : ''; ?>>800x600</option>
        <option value="960x720" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '960x720') ? 'selected' : ''; ?>>960x720</option>
        <option value="1024x768" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '1024x768') ? 'selected' : ''; ?>>1024x768</option>
        <option value="1152x864" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '1152x864') ? 'selected' : ''; ?>>1152x864</option>
        <option value="1280x960" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '1280x960') ? 'selected' : ''; ?>>1280x960</option>
        <option value="1400x1050" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '1400x1050') ? 'selected' : ''; ?>>1400x1050</option>
        <option value="1440x1080" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '1440x1080') ? 'selected' : ''; ?>>1440x1080</option>
        <option value="1600x1200" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '1600x1200') ? 'selected' : ''; ?>>1600x1200</option>
        <option value="1856x1392" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '1856x1392') ? 'selected' : ''; ?>>1856x1392</option>
        <option value="1920x1440" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '1920x1440') ? 'selected' : ''; ?>>1920x1440</option>
        <option value="2048x1536" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '2048x1536') ? 'selected' : ''; ?>>2048x1536</option>
        <!-- 16/9 -->
        <option disabled>16/9 resolutions:</option>
        <option value="1280x720" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '1280x720') ? 'selected' : ''; ?>>1280x720 (720p)</option>
        <option value="1920x1080" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '1920x1080') ? 'selected' : ''; ?>>1920x1080 (1080p)</option>
        <option value="2560x1440" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '2560x1440') ? 'selected' : ''; ?>>2560x1440 (1440p)</option>
        <option value="3840x2160" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '3840x2160') ? 'selected' : ''; ?>>3840x2160 (2160p)</option>
        <option value="5120x2880" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '5120x2880') ? 'selected' : ''; ?>>5120x2880 (2880p)</option>
        <option value="7680x4320" <?php echo ($cameraRawParams['width'] . 'x' . $cameraRawParams['height'] == '7680x4320') ? 'selected' : ''; ?>>7680x4320 (4320p)</option>
    </select>

    <h6>FRAME RATE</h6>
    <p class="note">Set to 0 to use the default frame rate of the camera.</p>
    <input type="number" class="form-param" param-name="framerate" value="<?= $cameraRawParams['framerate'] ?>" min="0" />

    <h6>ROTATE</h6>
    <p class="note">Set to 0 to disable rotation.</p>
    <select class="form-param" param-name="rotate">
        <option value="0" <?php echo $cameraRawParams['rotate'] == "0" ? 'selected' : '' ?>>0</option>
        <option value="90" <?php echo $cameraRawParams['rotate'] == "90" ? 'selected' : '' ?>>90</option>
        <option value="180" <?php echo $cameraRawParams['rotate'] == "180" ? 'selected' : '' ?>>180</option>
        <option value="270" <?php echo $cameraRawParams['rotate'] == "270" ? 'selected' : '' ?>>270</option>
    </select>

    <h6>TEXT LEFT</h6>
    <p class="note">Text to display on the left side of the camera feed.</p>
    <input type="text" class="form-param" param-name="text-left" value="<?= $cameraRawParams['text-left'] ?>" />

    <h6>TEXT RIGHT</h6>
    <p class="note">Text to display on the right side of the camera feed.</p>
    <input type="text" class="form-param" param-name="text-right" value="<?= $cameraRawParams['text-right'] ?>" />  
    
    <h6>TIMESTAMP LEFT</h6>
    <p class="note">Display timestamp on the left side of the camera feed.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="timestamp-left" <?php echo $cameraRawParams['timestamp-left'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6>TIMESTAMP RIGHT</h6>
    <p class="note">Display timestamp on the right side of the camera feed.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="timestamp-right" <?php echo $cameraRawParams['timestamp-right'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6>AUTHENTICATION</h6>
    <div class="basic-auth-fields">
        <p class="note">If your camera requires authentication (to access the video stream or ONVIF service).</p>

        <h6>USERNAME</h6>
        <input type="text" class="form-param" param-name="basic-auth-username" value="<?= $cameraRawParams['basic-auth-username'] ?>" />

        <h6>PASSWORD</h6>
        <input type="password" class="form-param" param-name="basic-auth-password" value="<?= $cameraRawParams['basic-auth-password'] ?>" />
    </div>

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

    <h6>DISPLAY LIVE STREAM</h6>
    <p class="note">Display camera output on the live stream page.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="stream-enable" <?php echo $cameraRawParams['stream-enable'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6>HARDWARE ACCELERATION</h6>
    <p class="note">Enable hardware acceleration for decoding and encoding video streams.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="hardware-acceleration" <?php echo $cameraRawParams['hardware-acceleration'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6>ENABLE MOTION DETECTION</h6>
    <p class="note">Enable motion detection for this camera.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="motion-detection-enable" <?php echo $cameraRawParams['motion-detection-enable'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6>MOTION CONFIGURATION</h6>
    <p class="note">Configure motion parameters for this camera. For advanced users only.</p>
    <button type="button" class="btn-medium-tr get-motion-config-form-btn margin-top-5" camera-id="<?= $cameraId ?>">Configure</button>

    <h6>ENABLE TIMELAPSE</h6>
    <p class="note">Enable timelapse for this camera.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="timelapse-enable" <?php echo $cameraRawParams['timelapse-enable'] == "true" ? 'checked' : '' ?>>
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