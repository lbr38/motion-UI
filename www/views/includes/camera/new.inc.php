<div id="new-camera-div" class="param-slide-container">
    <div class="param-slide">
        <img id="hide-new-camera-btn" src="resources/icons/error-close.svg" class="close-btn pointer lowopacity" title="Close" />

        <h2 class="center">Add a new camera</h2>



        <form id="new-camera-form" autocomplete="off">
            <span>Output type</span>
            <div class="switch-field">
                <input type="radio" id="outputType-video" name="output-type" value="video" checked />
                <label for="outputType-video">Video stream</label>
                <input type="radio" id="outputType-image" name="output-type" value="image" />
                <label for="outputType-image">Static JPEG image</label>
            </div>

            <span>Name</span>
            <input type="text" name="camera-name" placeholder="e.g. Outside camera" />

            <span>URL</span>
            <input type="text" name="camera-url" placeholder="e.g. http(s)://.../stream" />

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

            <br>
            <div>
                <p class="camera-stream-url hide yellowtext">Motion detection cannot work on static images. Specify a stream URL to use for Motion detection:</p>
            </div>
            <span class="camera-stream-url hide">Stream URL</span>
            <input class="camera-stream-url hide" type="text" name="camera-stream-url" placeholder="e.g. http(s)://.../stream" />

            <p><br><b>HTTP Authentication</b></p>
            <span></span>

            <span>Username</span>
            <input type="text" name="camera-username" />

            <span>Password</span>
            <input type="password" name="camera-password" />

            <button type="submit" class="btn-small-green">Add</button>
        </form>
    </div>
</div>