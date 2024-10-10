<?php ob_start(); ?> 

<form id="new-camera-form" autocomplete="off">
    <h4>Global settings</h4>

    <h6>NAME</h6>
    <input type="text" class="form-param" param-name="name" placeholder="e.g. Outside camera" />

    <h6>DEVICE or URL</h6>
    <input type="text" class="form-param" param-name="url" placeholder="e.g. /dev/video0 or http(s)://... or rtsp://..." />

    <h6>RESOLUTION</h6>
    <select class="form-param" param-name="resolution">
        <!-- 4/3 -->
        <option disabled>4/3 resolutions:</option>
        <option value="640x480" selected>640x480</option>
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
        <!-- 16/9 -->
        <option disabled>16/9 resolutions:</option>
        <option value="1280x720">1280x720 (720p)</option>
        <option value="1920x1080">1920x1080 (1080p)</option>
        <option value="2560x1440">2560x1440 (1440p)</option>
        <option value="3840x2160">3840x2160 (2160p)</option>
        <option value="5120x2880">5120x2880 (2880p)</option>
        <option value="7680x4320">7680x4320 (4320p)</option>
    </select>

    <h6>FRAME RATE</h6>
    <p class="input-note">Set to 0 to use the default frame rate of the camera.</p>
    <input type="number" class="form-param" param-name="framerate" value="0" min="0" />

    <h6>HTTP AUTHENTICATION</h6>
    <label class="onoff-switch-label">
        <input class="onoff-switch-input basic-auth-switch" type="checkbox">
        <span class="onoff-switch-slider"></span>
    </label>
    
    <div class="basic-auth-fields hide">
        <p class="lowopacity-cst margin-bottom-5">Be aware that credentials will be stored in plain text in the database as camera authentication is using <code>Basic</code> HTTP Authentication.</p>

        <h6>USERNAME</h6>
        <input type="text" class="form-param" param-name="basic-auth-username" />

        <h6>PASSWORD</h6>
        <input type="password" class="form-param" param-name="basic-auth-password" />
    </div>

    <h4>Motion detection</h4>

    <h6>ENABLE MOTION DETECTION</h6>
    <label class="onoff-switch-label">
        <input type="checkbox" class="onoff-switch-input form-param" param-name="motion-detection-enable" checked>
        <span class="onoff-switch-slider"></span>
    </label>

    <br><br>
    <button type="submit" class="btn-small-green">Add</button>
</form>

<?php
$content = ob_get_clean();
$slidePanelName = 'new-camera';
$slidePanelTitle = 'NEW CAMERA';

include(ROOT . '/views/includes/slide-panel.inc.php');