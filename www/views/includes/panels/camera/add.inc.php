<?php ob_start(); ?> 

<form id="new-camera-form" autocomplete="off">
    <div class="margin-left-15">
        <div class="grid grid-fr-1-2 align-item-center column-gap-10 row-gap-10">
            <span>Name</span>
            <input type="text" name="camera-name" placeholder="e.g. Outside camera" />

            <span>URL</span>
            <input type="text" name="camera-url" placeholder="e.g. http(s)://.../stream" />

            <span>Output type</span>
            <div class="switch-field">
                <input type="radio" id="outputType-video" name="output-type" value="video" checked />
                <label for="outputType-video">Video stream</label>
                <input type="radio" id="outputType-image" name="output-type" value="image" />
                <label for="outputType-image">Static JPEG image</label>
            </div>

            <span>Output resolution</span>
            <select name="output-resolution">
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

            <span class="camera-refresh-field hide">Refresh image (sec.)</span>
            <input class="camera-refresh-field hide" type="number" name="camera-refresh" value="3" />

            <span>Display camera live stream</span>
            <label class="onoff-switch-label">
                <input class="onoff-switch-input" type="checkbox" name="camera-live-enable" checked>
                <span class="onoff-switch-slider"></span>
            </label>

            <span>Enable motion detection</span>
            <label class="onoff-switch-label">
                <input class="onoff-switch-input" type="checkbox" name="camera-motion-enable" checked>
                <span class="onoff-switch-slider"></span>
            </label>

            <span>Enable timelapse</span>
            <label class="onoff-switch-label">
                <input class="onoff-switch-input" type="checkbox" name="camera-timelapse-enable">
                <span class="onoff-switch-slider"></span>
            </label>
        </div>

        <p class="camera-stream-url hide yellowtext margin-bottom-10">Motion detection cannot work on static images. Specify a stream URL to use for Motion detection:</p>

        <div class="grid grid-fr-1-2 align-item-center column-gap-10 row-gap-10">
            <span class="camera-stream-url hide">Stream URL</span>
            <input class="camera-stream-url hide" type="text" name="camera-stream-url" placeholder="e.g. http(s)://.../stream" />
        </div>

        <p class="margin-top-15 margin-bottom-15">HTTP Authentication</p>

        <p class="lowopacity-cst margin-bottom-5">Be aware that credentials will be stored in plain text in the database as camera authentication is using <code>Basic</code> HTTP Authentication.</p>

        <div class="grid grid-fr-1-2 align-item-center column-gap-10 row-gap-10">
            <span>Username</span>
            <input type="text" name="camera-username" />
            <span>Password</span>
            <input type="password" name="camera-password" />
        </div>

        <br>
        <button type="submit" class="btn-small-green">Add</button>
    </div>
</form>

<?php
$content = ob_get_clean();
$slidePanelName = 'new-camera';
$slidePanelTitle = 'NEW CAMERA';

include(ROOT . '/views/includes/slide-panel.inc.php');