<?php ob_start(); ?>

<h6><?= __('HOME PAGE') ?></h6>

<form id="settings-form">
    <p class="note"><?= __('Motion-UI start page.') ?></p>
    <select class="settings-param" setting-name="home-page">
        <option value="live" <?php echo (HOME_PAGE === 'live') ? 'selected' : '' ?>><?= __('Cameras and stream page') ?></option>
        <option value="motion" <?php echo (HOME_PAGE === 'motion') ? 'selected' : '' ?>><?= __('Motion buttons page') ?></option>
        <option value="events" <?php echo (HOME_PAGE === 'events') ? 'selected' : '' ?>><?= __('Motion events page') ?></option>
        <option value="stats" <?php echo (HOME_PAGE === 'stats') ? 'selected' : '' ?>><?= __('Motion stats page') ?></option>
    </select>

    <h5><?= __('MOTION DETECTION') ?></h5>

    <h6><?= __('EVENTS MEDIAS RETENTION') ?></h6>
    <p class="note"><?= __('Number of days to keep event pictures & videos before automatic deletion.') ?></p>
    <input type="number" min="1" class="settings-param" setting-name="motion-events-retention" value="<?= MOTION_EVENTS_RETENTION ?>">

    <h5><?= __('TIMELAPSE') ?></h5>

    <h6><?= __('INTERVAL') ?></h6>
    <p class="note"><?= __('Interval between each image capture.') ?></p>
    <select class="settings-param" setting-name="timelapse-interval">
        <option value="5" <?php echo (TIMELAPSE_INTERVAL == '5') ? 'selected' : ''?>><?= __('5 seconds') ?></option>
        <option value="10" <?php echo (TIMELAPSE_INTERVAL == '10') ? 'selected' : ''?>><?= __('10 seconds') ?></option>
        <option value="15" <?php echo (TIMELAPSE_INTERVAL == '15') ? 'selected' : ''?>><?= __('15 seconds') ?></option>
        <option value="30" <?php echo (TIMELAPSE_INTERVAL == '30') ? 'selected' : ''?>><?= __('30 seconds') ?></option>
        <option value="60" <?php echo (TIMELAPSE_INTERVAL == '60') ? 'selected' : ''?>><?= __('1 minute') ?></option>
        <option value="300" <?php echo (TIMELAPSE_INTERVAL == '300') ? 'selected' : ''?>><?= __('5 minutes') ?></option>
        <option value="600" <?php echo (TIMELAPSE_INTERVAL == '600') ? 'selected' : ''?>><?= __('10 minutes') ?></option>
        <option value="900" <?php echo (TIMELAPSE_INTERVAL == '900') ? 'selected' : ''?>><?= __('15 minutes') ?></option>
        <option value="1800" <?php echo (TIMELAPSE_INTERVAL == '1800') ? 'selected' : ''?>><?= __('30 minutes') ?></option>
        <option value="3600" <?php echo (TIMELAPSE_INTERVAL == '3600') ? 'selected' : ''?>><?= __('1 hour') ?></option>
    </select>

    <h6><?= __('IMAGES RETENTION') ?></h6>
    <p class="note"><?= __('Number of days to keep timelapse images before automatic deletion.') ?></p>
    <input type="number" min="1" class="settings-param" setting-name="timelapse-retention" value="<?= TIMELAPSE_RETENTION ?>">

    <h6><?= __('DEBUG MODE') ?></h6>
    <p class="note"><?= __('Debug mode will display additional information on the interface.') ?></p>
    <label class="onoff-switch-label">
        <input id="debug-mode-btn" class="onoff-switch-input" type="checkbox" value="true" <?php echo \Controllers\App\DebugMode::enabled() === true ? 'checked' : ''; ?>>
        <span class="onoff-switch-slider"></span>
    </label>

    <br>
    <br>
    <button type="submit" class="btn-small-green" title="<?= __('Save') ?>"><?= __('Save') ?></button>
</form>

<?php
$content = ob_get_clean();
$slidePanelName = 'general/settings';
$slidePanelTitle = __('GLOBAL SETTINGS');

include(ROOT . '/views/includes/slide-panel.inc.php');