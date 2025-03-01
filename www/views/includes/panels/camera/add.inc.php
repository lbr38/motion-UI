<?php ob_start(); ?> 

<form id="new-camera-form" autocomplete="off">
    <h6 class="required">NAME</h6>
    <p class="note">Example: Outside camera</p>
    <input type="text" class="form-param" param-name="name" />

    <h6 class="required">CAMERA MAIN STREAM</h6>
    <p class="note">Device path like /dev/video0 or URL like http://... or rtsp://... are supported.</p>
    <input type="text" class="form-param" param-name="main-stream-device" />

    <h6 class="required">RESOLUTION</h6>
    <p class="note">The selected resolution must match the resolution of the camera.</p>
    <select class="form-param" param-name="main-stream-resolution">
        <!-- 16/9 -->
        <option disabled>16/9 resolutions:</option>
        <option value="640x360" selected>640x360 (360p)</option>
        <option value="854x480">854x480 (480p)</option>
        <option value="960x540">960x540 (540p)</option>
        <option value="1024x576">1024x576 (576p)</option>
        <option value="1280x720">1280x720 (720p)</option>
        <option value="1920x1080">1920x1080 (1080p)</option>
        <option value="2560x1440">2560x1440 (1440p)</option>
        <option value="3840x2160">3840x2160 (2160p)</option>
        <option value="5120x2880">5120x2880 (2880p)</option>
        <option value="7680x4320">7680x4320 (4320p)</option>
        <!-- 4/3 -->
        <option disabled>4/3 resolutions:</option>
        <option value="640x480">640x480</option>
        <option value="800x600">800x600</option>
        <option value="960x720">960x720</option>
        <option value="1024x768">1024x768</option>
        <option value="1152x864">1152x864</option>
        <option value="1280x960">1280x960</option>
        <option value="1400x1050">1400x1050</option>
        <option value="1440x1080">1440x1080</option>
        <option value="1600x1200">1600x1200</option>
        <option value="1856x1392">1856x1392</option>
        <option value="1920x1440">1920x1440</option>
        <option value="2048x1536">2048x1536</option>
    </select>

    <h6>FRAME RATE</h6>
    <p class="note">The specified frame rate must match the frame rate of the camera.</p>
    <input type="number" class="form-param" param-name="main-stream-framerate" value="25" min="2" />

    <h6>AUTHENTICATION</h6>
    <p class="note">If your camera requires authentication (to access the video stream or ONVIF service).</p>
    <label class="onoff-switch-label">
        <input class="onoff-switch-input" type="checkbox">
        <span class="onoff-switch-slider toggle-btn" target=".basic-auth-fields"></span>
    </label>
    
    <div class="basic-auth-fields hide">
        <h6>USERNAME</h6>
        <input type="text" class="form-param" param-name="username" />

        <h6>PASSWORD</h6>
        <input type="password" class="form-param" param-name="password" />
    </div>

    <h6>ENABLE MOTION DETECTION</h6>
    <p class="note">Enable motion detection for this camera.</p>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="motion-detection-enable" checked>
        <span class="onoff-switch-slider"></span>
    </label>

    <br><br>
    <button type="submit" class="btn-small-green">Add</button>
</form>

<?php
$content = ob_get_clean();
$slidePanelName = 'camera/add';
$slidePanelTitle = 'NEW CAMERA';

include(ROOT . '/views/includes/slide-panel.inc.php');