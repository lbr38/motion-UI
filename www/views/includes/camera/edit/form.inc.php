<div class="flex align-item-center justify-space-between">
    <div>
        <h6 class="margin-top-0">ID</h6>
        <p>#<?= $cameraId ?></p>
    </div>
</div>

<form id="edit-global-settings-form" camera-id="<?= $cameraId ?>" autocomplete="off">
    <h6 class="required"><?= $_['h6']['name'] ?></h6>
    <input type="text" class="form-param" param-name="name" value="<?= $cameraRawParams['name'] ?>" />

    <h6 class="required"><?= $_['h6']['main_stream'] ?></h6>
    <p class="note"><?= $_['p']['main_stream_note'] ?></p>
    <input type="text" class="form-param" param-name="main-stream-device" value="<?= $cameraRawParams['main-stream']['device'] ?>" placeholder="e.g. /dev/video0 or http(s)://... or rtsp://..." />

    <h6 class="required"><?= $_['h6']['resolution'] ?></h6>
    <p class="note"><?= $_['p']['resolution_note'] ?></p>
    <div class="flex align-item-center column-gap-10">
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
        <img src="/assets/icons/loading.svg" class="icon-np main-stream-resolution-loading hide" />
    </div>

    <h6><?= $_['h6']['framerate'] ?></h6>
    <p class="note"><?= $_['p']['framerate_note'] ?></p>
    <div class="flex align-item-center column-gap-10">
        <input type="number" class="form-param" param-name="main-stream-framerate" value="<?= $cameraRawParams['main-stream']['framerate'] ?>" min="2" />
        <img src="/assets/icons/loading.svg" class="icon-np main-stream-framerate-loading hide" />
    </div>

    <h6><?= $_['h6']['rotate'] ?></h6>
    <p class="note"><?= $_['p']['rotate_note'] ?></p>
    <select class="form-param" param-name="main-stream-rotate">
        <option value="0" <?php echo $cameraRawParams['main-stream']['rotate'] == "0" ? 'selected' : '' ?>>0</option>
        <option value="90" <?php echo $cameraRawParams['main-stream']['rotate'] == "90" ? 'selected' : '' ?>>90</option>
        <option value="180" <?php echo $cameraRawParams['main-stream']['rotate'] == "180" ? 'selected' : '' ?>>180</option>
        <option value="270" <?php echo $cameraRawParams['main-stream']['rotate'] == "270" ? 'selected' : '' ?>>270</option>
    </select>

    <h6><?= $_['h6']['text_left'] ?></h6>
    <p class="note"><?= $_['p']['text_left_note'] ?></p>
    <input type="text" class="form-param" param-name="main-stream-text-left" value="<?= $cameraRawParams['main-stream']['text-left'] ?>" />

    <h6><?= $_['h6']['text_right'] ?></h6>
    <p class="note"><?= $_['p']['text_right_note'] ?></p>
    <input type="text" class="form-param" param-name="main-stream-text-right" value="<?= $cameraRawParams['main-stream']['text-right'] ?>" />  
    
    <h6><?= $_['h6']['timestamp_left'] ?></h6>
    <p class="note"><?= $_['p']['timestamp_left_note'] ?></p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="main-stream-timestamp-left" <?php echo $cameraRawParams['main-stream']['timestamp-left'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6><?= $_['h6']['timestamp_right'] ?></h6>
    <p class="note"><?= $_['p']['timestamp_right_note'] ?></p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="main-stream-timestamp-right" <?php echo $cameraRawParams['main-stream']['timestamp-right'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <hr class="margin-top-20 margin-bottom-20">

    <h6><?= $_['h6']['secondary_stream'] ?></h6>
    <p class="note"><?= $_['p']['secondary_stream_note'] ?></p>
    <input type="text" class="form-param" param-name="secondary-stream-device" value="<?= $cameraRawParams['secondary-stream']['device'] ?>" placeholder="e.g. http(s)://... or rtsp://..." />

    <h6><?= $_['h6']['secondary_stream_resolution'] ?></h6>
    <p class="note"><?= $_['p']['secondary_stream_resolution_note'] ?></p>
    <div class="flex align-item-center column-gap-10">
        <select class="form-param" param-name="secondary-stream-resolution">
            <!-- 16/9 -->
            <option disabled>16/9</option>
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
            <option disabled>4/3</option>
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
        <img src="/assets/icons/loading.svg" class="icon-np secondary-stream-resolution-loading hide" />
    </div>

    <h6><?= $_['h6']['secondary_stream_framerate'] ?></h6>
    <p class="note"><?= $_['p']['secondary_stream_framerate_note'] ?></p>
    <div class="flex align-item-center column-gap-10">
        <input type="number" class="form-param" param-name="secondary-stream-framerate" value="<?= $cameraRawParams['secondary-stream']['framerate'] ?>" min="2" />
        <img src="/assets/icons/loading.svg" class="icon-np secondary-stream-framerate-loading hide" />
    </div>

    <hr class="margin-top-20 margin-bottom-20">

    <h6><?= $_['h6']['authentication'] ?></h6>
    <p class="note"><?= $_['p']['authentication_note'] ?></p>
    <label class="onoff-switch-label">
        <input class="onoff-switch-input" type="checkbox" <?php echo (!empty($cameraRawParams['authentication']['username']) || !empty($cameraRawParams['authentication']['password'])) ? 'checked' : ''; ?>>
        <span class="onoff-switch-slider toggle-btn" target=".basic-auth-fields"></span>
    </label>

    <div class="basic-auth-fields <?php echo (empty($cameraRawParams['authentication']['username']) && empty($cameraRawParams['authentication']['password'])) ? 'hide' : ''; ?>">
        <h6><?= $_['h6']['authentication_username'] ?></h6>
        <input type="text" class="form-param" param-name="username" value="<?= $cameraRawParams['authentication']['username'] ?>" />

        <h6><?= $_['h6']['authentication_password'] ?></h6>
        <input type="password" class="form-param" param-name="password" value="<?= $cameraRawParams['authentication']['password'] ?>" />
    </div>

    <hr class="margin-top-20 margin-bottom-20">

    <h6><?= $_['h6']['enable_onvif'] ?></h6>
    <p class="note"><?= $_['p']['enable_onvif_note'] ?></p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="onvif-enable" <?php echo ($cameraRawParams['onvif']['enable'] == 'true') ? 'checked' : ''; ?> >
        <span class="onoff-switch-slider"></span>
    </label>

    <div id="onvif-fields" class="<?= $onvifFieldsClass ?>">
        <h6><?= $_['h6']['onvif_port'] ?></h6>
        <p class="note"><?= $_['p']['onvif_port_note'] ?></p>
        <input type="number" class="form-param" param-name="onvif-port" value="<?= $cameraRawParams['onvif']['port'] ?>" />

        <!-- <h6>ONVIF URI</h6>
        <input type="text" class="form-param" param-name="onvif-uri" value="<?= $cameraRawParams['onvif']['uri'] ?>" placeholder="e.g. /onvif/device_service" /> -->

        <?php
        if (!empty($cameraRawParams['onvif']['url'])) {
            echo '<p class="note">Target URL: ' . $cameraRawParams['onvif']['url'] . '</p>';
        } ?>
    </div>

    <hr class="margin-top-20 margin-bottom-20">

    <h6><?= $_['h6']['enable_motion_detection'] ?></h6>
    <p class="note"><?= $_['p']['enable_motion_detection_note'] ?></p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="motion-detection-enable" <?php echo $cameraRawParams['motion-detection']['enable'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6><?= $_['h6']['configure_motion_detection'] ?></h6>
    <p class="note"><?= $_['p']['configure_motion_detection_note'] ?></p>
    <button type="button" class="btn-medium-tr get-motion-config-form-btn margin-top-5" camera-id="<?= $cameraId ?>">Configure</button>

    <hr class="margin-top-20 margin-bottom-20">

    <h6><?= $_['h6']['stream_technology'] ?></h6>
    <p class="note"><?= $_['p']['stream_technology_note'] ?></p>

    <select class="form-param" param-name="stream-technology">
        <option value="mse" <?php echo ($cameraRawParams['stream']['technology'] === 'mse') ? 'selected' : '' ?>>MSE</option>
        <option value="webrtc" <?php echo ($cameraRawParams['stream']['technology'] === 'webrtc') ? 'selected' : '' ?>>WebRTC</option>
        <option value="mjpeg" <?php echo ($cameraRawParams['stream']['technology'] === 'mjpeg') ? 'selected' : '' ?>>MJPEG</option>
    </select>

    <hr class="margin-top-20 margin-bottom-20">

    <h6><?= $_['h6']['enable_timelapse'] ?></h6>
    <p class="note"><?= $_['p']['enable_timelapse_note'] ?></p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="timelapse-enable" <?php echo $cameraRawParams['timelapse']['enable'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <hr class="margin-top-20 margin-bottom-20">

    <h6><?= $_['h6']['enable_monitoring'] ?></h6>
    <p class="note"><?= $_['p']['enable_monitoring_note'] ?></p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="monitoring-enable" <?php echo $cameraRawParams['monitoring']['enable'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <?php
    if (isset($cameraRawParams['monitoring']['enable']) and $cameraRawParams['monitoring']['enable'] == "true") : ?>
        <div class="monitoring-settings margin-top-15">
            <h6><?= $_['h6']['monitoring_recipients'] ?></h6>
            <p class="note"><?= $_['p']['monitoring_recipients_note'] ?></p>
            <select class="form-param" param-name="monitoring-recipients" multiple>
                <?php
                // Current recipients
                foreach ($cameraRawParams['monitoring']['recipients'] as $recipient) {
                    echo '<option value="' . $recipient . '" selected>' . $recipient . '</option>';
                }
                // All users emails
                foreach ($usersEmails as $email) {
                    if (!in_array($email, $cameraRawParams['monitoring']['recipients'])) {
                        echo '<option value="' . $email . '">' . $email . '</option>';
                    }
                } ?>
            </select>
        </div>
        <?php
    endif ?>

    <br><br>
    <div class="flex column-gap-10">
        <button type="submit" class="btn-small-green">Save</button>
        <button type="button" class="btn-small-red delete-camera-btn" title="Delete camera" camera-id="<?= $cameraId ?>">Delete</button>
    </div>
</form>

<br>
<br>

<script>
    myselect2.convert('select.form-param[param-name="monitoring-recipients"]', 'Select or specify recipients...', true, false);
</script>
