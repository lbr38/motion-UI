<div id="settings-div" class="param-slide-container">
    <div class="param-slide">
        <img id="hide-settings-btn" src="resources/icons/error-close.svg" class="close-btn lowopacity" title="Close" />

        <h2 class="center">Settings</h2>

        <div id="settings">
            <h4>Quick action buttons</h4>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-start-btn" <?php echo ($settings['Motion_start_btn'] == 'true') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Motion start/stop button</span>
            </div>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-autostart-btn" <?php echo ($settings['Motion_autostart_btn'] == 'true') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Motion autostart button</span>
            </div>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-alert-btn" <?php echo ($settings['Motion_alert_btn'] == 'true') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Motion alerts button</span>
            </div>

            <h4>Motion events</h4>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-events" <?php echo ($settings['Motion_events']  == 'true') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Print motion events</span>
            </div>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-events-videos-thumbnail" <?php echo ($settings['Motion_events_videos_thumbnail']  == 'true') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Print videos thumbnail</span>
            </div>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-events-pictures-thumbnail" <?php echo ($settings['Motion_events_pictures_thumbnail']  == 'true') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Print pictures thumbnail</span>
            </div>

            <h4>Motion stats</h4>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-stats" <?php echo ($settings['Motion_stats'] == 'true') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Print stats charts</span>
            </div>

            <h4>Cameras live stream</h4>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="stream-main-page" <?php echo ($settings['Stream_on_main_page'] == 'true') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Display stream on main page</span>
            </div>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="stream-live-page" <?php echo ($settings['Stream_on_live_page'] == 'true') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Display stream on a separate page (Live page)</span>
            </div>

            <br>
            <br>
            <button type="button" id="save-settings-btn" class="btn-small-green" title="Save">Save</button>
        </div>
    </div>
</div>