<?php ob_start(); ?>

<h4>Home page</h4>

<p class="lowopacity-cst">Motion-UI start page</p>

<div class="flex align-item-center column-gap-5">
    <select class="settings-param" setting-name="home-page">
        <option value="live" <?php echo (HOME_PAGE === 'live') ? 'selected' : ''?>>Cameras and stream page</option>
        <option value="motion" <?php echo (HOME_PAGE === 'motion') ? 'selected' : ''?>>Motion buttons page</option>
        <option value="events" <?php echo (HOME_PAGE === 'events') ? 'selected' : ''?>>Motion events page</option>
        <option value="stats" <?php echo (HOME_PAGE === 'stats') ? 'selected' : ''?>>Motion stats page</option>
    </select>
</div>

<h4>Motion events</h4>

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
    Media retention
    <br>
    <span class="lowopacity-cst">Number of days to keep event pictures & videos before automatic deletion</span>
</p>

<div class="flex align-item-center column-gap-5">
    <input type="number" class="settings-param" setting-name="motion-events-retention" value="<?= MOTION_EVENTS_RETENTION ?>">
</div>


<br>
<br>
<button type="button" id="save-settings-btn" class="btn-small-green" title="Save">Save</button>

<?php
$content = ob_get_clean();
$slidePanelName = 'settings';
$slidePanelTitle = 'SETTINGS';

include(ROOT . '/views/includes/slide-panel.inc.php');