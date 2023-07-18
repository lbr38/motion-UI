<?php ob_start(); ?>

<h4>Quick action buttons</h4>

<div class="flex align-item-center column-gap-5">
    <label class="onoff-switch-label">
        <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-start-btn" <?php echo (MOTION_START_BTN === true) ? 'checked' : ''?>>
        <span class="onoff-switch-slider"></span>
    </label>
    <span>Motion start/stop button</span>
</div>

<div class="flex align-item-center column-gap-5">
    <label class="onoff-switch-label">
        <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-autostart-btn" <?php echo (MOTION_AUTOSTART_BTN === true) ? 'checked' : ''?>>
        <span class="onoff-switch-slider"></span>
    </label>
    <span>Motion autostart button</span>
</div>

<div class="flex align-item-center column-gap-5">
    <label class="onoff-switch-label">
        <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-alert-btn" <?php echo (MOTION_ALERT_BTN === true) ? 'checked' : ''?>>
        <span class="onoff-switch-slider"></span>
    </label>
    <span>Motion alerts button</span>
</div>

<h4>Motion events</h4>

<div class="flex align-item-center column-gap-5">
    <label class="onoff-switch-label">
        <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-events" <?php echo (MOTION_EVENTS === true) ? 'checked' : ''?>>
        <span class="onoff-switch-slider"></span>
    </label>
    <span>Print motion events</span>
</div>

<div class="flex align-item-center column-gap-5">
    <label class="onoff-switch-label">
        <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-events-videos-thumbnail" <?php echo (MOTION_EVENTS_VIDEOS_THUMBNAIL === true) ? 'checked' : ''?>>
        <span class="onoff-switch-slider"></span>
    </label>
    <span>Print videos thumbnail</span>
</div>

<div class="flex align-item-center column-gap-5">
    <label class="onoff-switch-label">
        <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-events-pictures-thumbnail" <?php echo (MOTION_EVENTS_PICTURES_THUMBNAIL === true) ? 'checked' : ''?>>
        <span class="onoff-switch-slider"></span>
    </label>
    <span>Print pictures thumbnail</span>
</div>

<br>

<p>
    Medias retention
    <br>
    <span class="lowopacity-cst">Number of days to keep event pictures and videos before automatic deletion</span>
</p>

<div class="flex align-item-center column-gap-5">
    <input type="number" class="settings-param" setting-name="motion-events-retention" value="<?= MOTION_EVENTS_RETENTION ?>">
</div>



<h4>Motion stats</h4>

<div class="flex align-item-center column-gap-5">
    <label class="onoff-switch-label">
        <input class="onoff-switch-input settings-param" type="checkbox" setting-name="motion-stats" <?php echo (MOTION_STATS === true) ? 'checked' : ''?>>
        <span class="onoff-switch-slider"></span>
    </label>
    <span>Print stats charts</span>
</div>

<h4>Cameras live stream</h4>

<div class="flex align-item-center column-gap-5">
    <label class="onoff-switch-label">
        <input class="onoff-switch-input settings-param" type="checkbox" setting-name="stream-main-page" <?php echo (STREAM_ON_MAIN_PAGE === true) ? 'checked' : ''?>>
        <span class="onoff-switch-slider"></span>
    </label>
    <span>Display stream on main page</span>
</div>

<div class="flex align-item-center column-gap-5">
    <label class="onoff-switch-label">
        <input class="onoff-switch-input settings-param" type="checkbox" setting-name="stream-live-page" <?php echo (STREAM_ON_LIVE_PAGE === true) ? 'checked' : ''?>>
        <span class="onoff-switch-slider"></span>
    </label>
    <span>Display stream on a separate page (Live page)</span>
</div>

<br>
<br>
<button type="button" id="save-settings-btn" class="btn-small-green" title="Save">Save</button>

<?php
$content = ob_get_clean();
$slidePanelName = 'settings';
$slidePanelTitle = 'SETTINGS';

include(ROOT . '/views/includes/slide-panel.inc.php');