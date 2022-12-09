<div id="new-camera-div" class="param-slide-container">
    <div class="param-slide">
        <img id="hide-new-camera-btn" src="resources/icons/error-close.svg" class="close-btn pointer lowopacity" title="Close" />

        <h2 class="center">Add a new camera</h2>

        <span class="block center lowopacity">Add a MJPEG-stream based camera (mjpg_streamer, ustreamer...)</span>
        <br>

        <form id="new-camera-form" autocomplete="off">
            <p>Name:</p>
            <input type="text" name="camera-name" />

            <p>URL:<img src="resources/icons/info.svg" class="icon-lowopacity" title="Insert an URL that points directly to a JPEG image." /></p>
            <input type="text" name="camera-url" placeholder="e.g. http(s)://.../snapshot" />
            
            <br><br>
            
            <button type="submit" class="btn-small-green">Add</button>
        </form>
    </div>
</div>