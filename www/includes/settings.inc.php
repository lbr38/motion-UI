<div id="settings-div" class="param-slide-container">
    <div class="param-slide">
        <img id="hide-settings-btn" src="resources/icons/error-close.svg" class="close-btn lowopacity" title="Close" />

        <h2 class="center">Settings</h2>

        <div id="settings">

            <h2>Buttons</h2>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="print-live-btn" <?php echo ($settings['Print_live_btn'] == 'yes') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Live button</span>
            </div>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="print-motion-start-btn" <?php echo ($settings['Print_motion_start_btn'] == 'yes') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Motion start/stop button</span>
            </div>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="print-motion-autostart-btn" <?php echo ($settings['Print_motion_autostart_btn'] == 'yes') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Motion autostart button</span>
            </div>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="print-motion-alert-btn" <?php echo ($settings['Print_motion_alert_btn'] == 'yes') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Motion alerts button</span>
            </div>

            <h2>Motion events</h2>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="print-motion-captures-btn" <?php echo ($settings['Print_motion_events']  == 'yes') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Print motion events and captures</span>
            </div>

            <h2>Motion metrics</h2>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="print-motion-stats-btn" <?php echo ($settings['Print_motion_stats'] == 'yes') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Print charts</span>
            </div>

            <h2>Motion configuration</h2>

            <div class="flex">
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input settings-param" type="checkbox" setting-name="print-motion-config-btn" <?php echo ($settings['Print_motion_config']  == 'yes') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
                <span>Print motion configuration files</span>
            </div>

            <br>
            <br>
            <button type="button" id="save-settings-btn" class="btn-small-green" title="Save">Save</button>
        </div>
    </div>
</div>