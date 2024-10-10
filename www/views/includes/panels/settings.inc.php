<?php ob_start(); ?>

<h6>HOME PAGE</h6>
<p class="input-note">Motion-UI start page.</p>
<select class="settings-param" setting-name="home-page">
    <option value="live" <?php echo (HOME_PAGE === 'live') ? 'selected' : ''?>>Cameras and stream page</option>
    <option value="motion" <?php echo (HOME_PAGE === 'motion') ? 'selected' : ''?>>Motion buttons page</option>
    <option value="events" <?php echo (HOME_PAGE === 'events') ? 'selected' : ''?>>Motion events page</option>
    <option value="stats" <?php echo (HOME_PAGE === 'stats') ? 'selected' : ''?>>Motion stats page</option>
</select>

<h4>Timelapse</h4>

<h6>INTERVAL</h6>
<p class="input-note">Interval between each image capture.</p>
<select class="settings-param" setting-name="timelapse-interval">
    <option value="5" <?php echo (TIMELAPSE_INTERVAL == '5') ? 'selected' : ''?>>5 seconds</option>
    <option value="10" <?php echo (TIMELAPSE_INTERVAL == '10') ? 'selected' : ''?>>10 seconds</option>
    <option value="15" <?php echo (TIMELAPSE_INTERVAL == '15') ? 'selected' : ''?>>15 seconds</option>
    <option value="30" <?php echo (TIMELAPSE_INTERVAL == '30') ? 'selected' : ''?>>30 seconds</option>
    <option value="60" <?php echo (TIMELAPSE_INTERVAL == '60') ? 'selected' : ''?>>1 minute</option>
    <option value="300" <?php echo (TIMELAPSE_INTERVAL == '300') ? 'selected' : ''?>>5 minutes</option>
    <option value="600" <?php echo (TIMELAPSE_INTERVAL == '600') ? 'selected' : ''?>>10 minutes</option>
    <option value="900" <?php echo (TIMELAPSE_INTERVAL == '900') ? 'selected' : ''?>>15 minutes</option>
    <option value="1800" <?php echo (TIMELAPSE_INTERVAL == '1800') ? 'selected' : ''?>>30 minutes</option>
    <option value="3600" <?php echo (TIMELAPSE_INTERVAL == '3600') ? 'selected' : ''?>>1 hour</option>
</select>

<h6>IMAGES RETENTION</h6>
<p class="input-note">Number of days to keep timelapse images before automatic deletion.</p>
<input type="number" min="1" class="settings-param" setting-name="timelapse-retention" value="<?= TIMELAPSE_RETENTION ?>">

<h4>Motion events</h4>

<h6>MEDIAS RETENTION</h6>
<p class="input-note">Number of days to keep event pictures & videos before automatic deletion</p>
<input type="number" min="1" class="settings-param" setting-name="motion-events-retention" value="<?= MOTION_EVENTS_RETENTION ?>">

<br>
<br>
<button type="button" id="save-settings-btn" class="btn-small-green" title="Save">Save</button>

<?php
$content = ob_get_clean();
$slidePanelName = 'settings';
$slidePanelTitle = 'GLOBAL SETTINGS';

include(ROOT . '/views/includes/slide-panel.inc.php');