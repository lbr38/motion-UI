<?php ob_start(); ?>
        
<p>Motion detection alerts are sent on new event and when picture or video is saved.</p>

<h4>Alerts time period</h4>

<p class="input-note">For each day, define the time period in which alerts will be sent. Outside of this time period, no alert will be sent.</p>
<br>

<p class="input-note">Specify <b>Start:</b> <code>--:--</code> and <b>End:</b> <code>--:--</code> if you wish not to receive any alert on one specific day of the week.</p>
<br>
<p class="input-note">Specify <b>Start:</b> <code>00:00</code> and <b>End:</b> <code>00:00</code> if you wish to receive alerts <b>24hours a day</b>.</p>
<br>
<p class="input-note">Actual timezone: <?= date_default_timezone_get() ?></p>
<br>

<form id="alert-conf-form" autocomplete="off">
    <h6>MONDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="monday-start" value="<?= $alertConfiguration['Monday_start'] ?>" />
        <input type="time" name="monday-end" value="<?= $alertConfiguration['Monday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <h6>TUESDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="tuesday-start" value="<?= $alertConfiguration['Tuesday_start'] ?>" />
        <input type="time" name="tuesday-end" value="<?= $alertConfiguration['Tuesday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <h6>WEDNESDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="wednesday-start" value="<?= $alertConfiguration['Wednesday_start'] ?>" />
        <input type="time" name="wednesday-end" value="<?= $alertConfiguration['Wednesday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <h6>THURSDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="thursday-start" value="<?= $alertConfiguration['Thursday_start'] ?>" />
        <input type="time" name="thursday-end" value="<?= $alertConfiguration['Thursday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <h6>FRIDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="friday-start" value="<?= $alertConfiguration['Friday_start'] ?>" />
        <input type="time" name="friday-end" value="<?= $alertConfiguration['Friday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <h6>SATURDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="saturday-start" value="<?= $alertConfiguration['Saturday_start'] ?>" />
        <input type="time" name="saturday-end" value="<?= $alertConfiguration['Saturday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <h6>SUNDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="sunday-start" value="<?= $alertConfiguration['Sunday_start'] ?>" />
        <input type="time" name="sunday-end" value="<?= $alertConfiguration['Sunday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>
    
    <h6>MAIL RECIPIENT</h6>
    <input type="email" name="mail-recipient" value="<?= $alertConfiguration['Recipient'] ?>" multiple required />
    <p class="input-note">Specify mail alert recipient(s), separated by a comma.</p>

    <br>

    <div class="flex column-gap-10">
        <button type="submit" class="btn-small-green">Save</button>
        <?php
        if (!empty($alertConfiguration['Recipient'])) {
            echo '<br>';
            echo '<span id="send-test-email-btn" mail-recipient="' . $alertConfiguration['Recipient'] . '" class="btn-medium-tr">Send test email</span>';
        } ?>
    </div>
</form>



<br>
<br>

<?php
$content = ob_get_clean();
$slidePanelName = 'alert';
$slidePanelTitle = 'CONFIGURE ALERTS';

include(ROOT . '/views/includes/slide-panel.inc.php');