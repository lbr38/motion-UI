<h6>ID</h6>
<p>#<?= $camera['Id'] ?></p>

<form id="edit-global-settings-form" camera-id="<?= $camera['Id'] ?>" autocomplete="off">
    <h4>Global settings</h4>

    <h6>NAME</h6>
    <input type="text" class="form-param" param-name="name" value="<?= $camera['Name'] ?>" />
        
    <h6>DEVICE or URL</h6>
    <input type="text" class="form-param" param-name="url" value="<?= $camera['Url'] ?>" placeholder="e.g. /dev/video0 or http(s)://... or rtsp://..." />

    <h6>RESOLUTION</h6>
    <select class="form-param" param-name="resolution">
        <!-- 4/3 -->
        <option disabled>4/3 resolutions:</option>
        <option value="640x480" <?php echo ($camera['Output_resolution'] == '640x480') ? 'selected' : ''; ?>>640x480</option>
        <option value="800x600" <?php echo ($camera['Output_resolution'] == '800x600') ? 'selected' : ''; ?>>800x600</option>
        <option value="960x720" <?php echo ($camera['Output_resolution'] == '960x720') ? 'selected' : ''; ?>>960x720</option>
        <option value="1024x768" <?php echo ($camera['Output_resolution'] == '1024x768') ? 'selected' : ''; ?>>1024x768</option>
        <option value="1152x864" <?php echo ($camera['Output_resolution'] == '1152x864') ? 'selected' : ''; ?>>1152x864</option>
        <option value="1280x960" <?php echo ($camera['Output_resolution'] == '1280x960') ? 'selected' : ''; ?>>1280x960</option>
        <option value="1400x1050" <?php echo ($camera['Output_resolution'] == '1400x1050') ? 'selected' : ''; ?>>1400x1050</option>
        <option value="1440x1080" <?php echo ($camera['Output_resolution'] == '1440x1080') ? 'selected' : ''; ?>>1440x1080</option>
        <option value="1600x1200" <?php echo ($camera['Output_resolution'] == '1600x1200') ? 'selected' : ''; ?>>1600x1200</option>
        <option value="1856x1392" <?php echo ($camera['Output_resolution'] == '1856x1392') ? 'selected' : ''; ?>>1856x1392</option>
        <option value="1920x1440" <?php echo ($camera['Output_resolution'] == '1920x1440') ? 'selected' : ''; ?>>1920x1440</option>
        <option value="2048x1536" <?php echo ($camera['Output_resolution'] == '2048x1536') ? 'selected' : ''; ?>>2048x1536</option>
        <!-- 16/9 -->
        <option disabled>16/9 resolutions:</option>
        <option value="1280x720" <?php echo ($camera['Output_resolution'] == '1280x720') ? 'selected' : ''; ?>>1280x720 (720p)</option>
        <option value="1920x1080" <?php echo ($camera['Output_resolution'] == '1920x1080') ? 'selected' : ''; ?>>1920x1080 (1080p)</option>
        <option value="2560x1440" <?php echo ($camera['Output_resolution'] == '2560x1440') ? 'selected' : ''; ?>>2560x1440 (1440p)</option>
        <option value="3840x2160" <?php echo ($camera['Output_resolution'] == '3840x2160') ? 'selected' : ''; ?>>3840x2160 (2160p)</option>
        <option value="5120x2880" <?php echo ($camera['Output_resolution'] == '5120x2880') ? 'selected' : ''; ?>>5120x2880 (2880p)</option>
        <option value="7680x4320" <?php echo ($camera['Output_resolution'] == '7680x4320') ? 'selected' : ''; ?>>7680x4320 (4320p)</option>
    </select>

    <h6>FRAME RATE</h6>
    <p class="input-note">Set to 0 to use the default frame rate of the camera.</p>
    <input type="number" class="form-param" param-name="framerate" value="<?= $camera['Framerate'] ?>" min="0" />

    <h6>ROTATE</h6>
    <p class="input-note">Set to 0 to disable rotation.</p>
    <select class="form-param" param-name="rotate">
        <option value="0" <?php echo $camera['Rotate'] == "0" ? 'selected' : '' ?>>0</option>
        <option value="90" <?php echo $camera['Rotate'] == "90" ? 'selected' : '' ?>>90</option>
        <option value="180" <?php echo $camera['Rotate'] == "180" ? 'selected' : '' ?>>180</option>
        <option value="270" <?php echo $camera['Rotate'] == "270" ? 'selected' : '' ?>>270</option>
    </select>

    <h6>TEXT LEFT</h6>
    <input type="text" class="form-param" param-name="text-left" value="<?= $camera['Text_left'] ?>" />

    <h6>TEXT RIGHT</h6>
    <input type="text" class="form-param" param-name="text-right" value="<?= $camera['Text_right'] ?>" />  
    
    <h6>TIMESTAMP LEFT</h6>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="timestamp-left" <?php echo $camera['Timestamp_left'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6>TIMESTAMP RIGHT</h6>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="timestamp-right" <?php echo $camera['Timestamp_right'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6>HTTP AUTHENTICATION</h6>   
    <div class="basic-auth-fields">
        <p class="lowopacity-cst margin-bottom-5">Be aware that credentials will be stored in plain text in the database as camera authentication is using <code>Basic</code> HTTP Authentication.</p>

        <h6>USERNAME</h6>
        <input type="text" class="form-param" param-name="basic-auth-username" value="<?= $camera['Username'] ?>" />

        <h6>PASSWORD</h6>
        <input type="password" class="form-param" param-name="basic-auth-password" value="<?= $camera['Password'] ?>" />
    </div>

    <h4>Live stream</h4>

    <h6>DISPLAY LIVE STREAM</h6>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="live-enable" <?php echo $camera['Live_enabled'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h6>HARDWARE ACCELERATION</h6>
    <p class="input-note">Enable hardware acceleration for decoding and encoding video streams.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="hardware-acceleration" <?php echo $camera['Hardware_acceleration'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <h4>Motion detection</h4>

    <h6>ENABLE MOTION DETECTION</h6>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="motion-detection-enable" <?php echo $camera['Motion_enabled'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <br>

    <h6>CONFIGURATION</h6>
    <button type="button" class="btn-medium-tr get-motion-config-form-btn margin-top-5" camera-id="<?= $camera['Id'] ?>">Edit motion configuration</button>

    <h4>Timelapse</h4>

    <h6>ENABLE TIMELAPSE</h6>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="timelapse-enable" <?php echo $camera['Timelapse_enabled'] == "true" ? 'checked' : '' ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <br><br>
    <div class="flex column-gap-10">
        <button type="submit" class="btn-small-green">Save</button>
        <button type="button" class="btn-small-red delete-camera-btn" title="Delete camera" camera-id="<?= $camera['Id'] ?>">Delete</button>
    </div>
</form>

<br>
<br>