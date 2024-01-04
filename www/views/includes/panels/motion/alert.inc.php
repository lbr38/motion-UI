<?php ob_start(); ?>
        
<p>Alerts are sent on event start and on video/picture save.</p>

<br>
<h4>Alerts time period</h4>

<p class="lowopacity-cst">For each day, define the time period in which alerts will be sent. Outside of this time period, no alert will be sent.</p>
<br>

<p class="lowopacity-cst">Actual timezone: <?= date_default_timezone_get() ?></p>
<br>

<form id="alert-conf-form" autocomplete="off">
    <table class="config-table">
        <tr>
            <th class="td-30"></th>
            <th class="center">Start</th>
            <th class="center">End</th>
        </tr>
        <tr>
            <th class="td-10">Monday</th>
            <td><input type="time" name="monday-start" value="<?= $alertConfiguration['Monday_start'] ?>" /></td>
            <td><input type="time" name="monday-end" value="<?= $alertConfiguration['Monday_end'] ?>" /></td>
        </tr>
        <tr>
            <th class="td-10">Tuesday</th>
            <td><input type="time" name="tuesday-start" value="<?= $alertConfiguration['Tuesday_start'] ?>" /></td>
            <td><input type="time" name="tuesday-end" value="<?= $alertConfiguration['Tuesday_end'] ?>" /></td>
        </tr>
        <tr>
            <th class="td-10">Wednesday</th>
            <td><input type="time" name="wednesday-start" value="<?= $alertConfiguration['Wednesday_start'] ?>" /></td>
            <td><input type="time" name="wednesday-end" value="<?= $alertConfiguration['Wednesday_end'] ?>" /></td>
        </tr>
        <tr>
            <th class="td-10">Thursday</th>
            <td><input type="time" name="thursday-start" value="<?= $alertConfiguration['Thursday_start'] ?>" /></td>
            <td><input type="time" name="thursday-end" value="<?= $alertConfiguration['Thursday_end'] ?>" /></td>
        </tr>
        <tr>
            <th class="td-10">Friday</th>
            <td><input type="time" name="friday-start" value="<?= $alertConfiguration['Friday_start'] ?>" /></td>
            <td><input type="time" name="friday-end" value="<?= $alertConfiguration['Friday_end'] ?>" /></td>
        </tr>
        <tr>
            <th class="td-10">Saturday</th>
            <td><input type="time" name="saturday-start" value="<?= $alertConfiguration['Saturday_start'] ?>" /></td>
            <td><input type="time" name="saturday-end" value="<?= $alertConfiguration['Saturday_end'] ?>" /></td>
        </tr>
        <tr>
            <th class="td-10">Sunday</th>
            <td><input type="time" name="sunday-start" value="<?= $alertConfiguration['Sunday_start'] ?>" /></td>
            <td><input type="time" name="sunday-end" value="<?= $alertConfiguration['Sunday_end'] ?>" /></td>
        </tr>
        <tr title="Specify mail alert recipient(s), separated by a comma.">
            <th class="td-10">Mail recipient</th>
            <td colspan="2"><input type="email" name="mail-recipient" value="<?= $alertConfiguration['Recipient'] ?>" multiple required />
        </tr>
    </table>

    <br>
    <p class="lowopacity-cst">Specify <b>Start:</b> <code>--:--</code> and <b>End:</b> <code>--:--</code> if you wish not to receive any alert on one specific day of the week.</p>
    <br>
    <p class="lowopacity-cst">Specify <b>Start:</b> <code>00:00</code> and <b>End:</b> <code>00:00</code> if you wish to receive alerts <b>24hours a day</b>.</p>
    <br>

    <button type="submit" class="btn-small-green">Save</button>
</form>

<?php
if (!empty($alertConfiguration['Recipient'])) {
    echo '<br>';
    echo '<span id="send-test-email-btn" mail-recipient="' . $alertConfiguration['Recipient'] . '" class="btn-medium-yellow">Send test email</span>';
} ?>

<br>
<br>

<?php
$content = ob_get_clean();
$slidePanelName = 'alert';
$slidePanelTitle = 'CONFIGURE ALERTS';

include(ROOT . '/views/includes/slide-panel.inc.php');