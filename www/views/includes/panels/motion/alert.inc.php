<?php ob_start(); ?>
        
<p>Motion detection alerts are sent on new event and when picture or video is saved.</p>

<h5>ALERTS TIME PERIOD</h5>

<p class="note">For each day, define the time period in which alerts will be sent. Outside of this time period, no alert will be sent.</p>
<br>
<p class="note">Specify <b>Start:</b> <code>--:--</code> and <b>End:</b> <code>--:--</code> if you wish not to receive any alert on one specific day of the week.</p>
<p class="note">Specify <b>Start:</b> <code>00:00</code> and <b>End:</b> <code>00:00</code> if you wish to receive alerts <b>24hours a day</b>.</p>
<br>
<p class="note">Actual timezone: <?= date_default_timezone_get() ?></p>
<br>

<form id="alert-conf-form" autocomplete="off">
    <h6 class="required">MONDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="monday-start" value="<?= $alertConfiguration['Monday_start'] ?>" />
        <input type="time" name="monday-end" value="<?= $alertConfiguration['Monday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>

    <h6 class="required margin-top-0">TUESDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="tuesday-start" value="<?= $alertConfiguration['Tuesday_start'] ?>" />
        <input type="time" name="tuesday-end" value="<?= $alertConfiguration['Tuesday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>

    <h6 class="required margin-top-0">WEDNESDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="wednesday-start" value="<?= $alertConfiguration['Wednesday_start'] ?>" />
        <input type="time" name="wednesday-end" value="<?= $alertConfiguration['Wednesday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>

    <h6 class="required margin-top-0">THURSDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="thursday-start" value="<?= $alertConfiguration['Thursday_start'] ?>" />
        <input type="time" name="thursday-end" value="<?= $alertConfiguration['Thursday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>

    <h6 class="required margin-top-0">FRIDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="friday-start" value="<?= $alertConfiguration['Friday_start'] ?>" />
        <input type="time" name="friday-end" value="<?= $alertConfiguration['Friday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>

    <h6 class="required margin-top-0">SATURDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="saturday-start" value="<?= $alertConfiguration['Saturday_start'] ?>" />
        <input type="time" name="saturday-end" value="<?= $alertConfiguration['Saturday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>

    <h6 class="required margin-top-0">SUNDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="sunday-start" value="<?= $alertConfiguration['Sunday_start'] ?>" />
        <input type="time" name="sunday-end" value="<?= $alertConfiguration['Sunday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>
    
    <h6 class="required">MAIL RECIPIENT</h6>
    <p class="note">Specify mail alert recipient(s), separated by a comma.</p>
    <input type="email" name="mail-recipient" value="<?= $alertConfiguration['Recipient'] ?>" multiple required />

    <br><br>
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
$slidePanelName = 'motion/alert';
$slidePanelTitle = 'CONFIGURE ALERTS';

include(ROOT . '/views/includes/slide-panel.inc.php');