<?php ob_start(); ?>

<form id="autostart-conf-form" autocomplete="off">
    <br>
    <h4>Autostart time period</h4>
    <br>
    <table class="config-table">
        <tr>
            <th class="td-30"></th>
            <th class="center">Start</th>
            <th class="center">End</th>
        </tr>
        <tr>
            <th class="td-30">Monday</th>
            <td><input type="time" name="monday-start" value="<?= $autostartConfiguration['Monday_start'] ?>" /></td>
            <td><input type="time" name="monday-end" value="<?= $autostartConfiguration['Monday_end'] ?>" /></td>
        </tr>
        <tr>
            <th class="td-30">Tuesday</th>
            <td><input type="time" name="tuesday-start" value="<?= $autostartConfiguration['Tuesday_start'] ?>" /></td>
            <td><input type="time" name="tuesday-end" value="<?= $autostartConfiguration['Tuesday_end'] ?>" /></td>
        </tr>
        <tr>
            <th class="td-30">Wednesday</th>
            <td><input type="time" name="wednesday-start" value="<?= $autostartConfiguration['Wednesday_start'] ?>" /></td>
            <td><input type="time" name="wednesday-end" value="<?= $autostartConfiguration['Wednesday_end'] ?>" /></td>
        </tr>
        <tr>
            <th class="td-30">Thursday</th>
            <td><input type="time" name="thursday-start" value="<?= $autostartConfiguration['Thursday_start'] ?>" /></td>
            <td><input type="time" name="thursday-end" value="<?= $autostartConfiguration['Thursday_end'] ?>" /></td>
        </tr>
        <tr>
            <th class="td-30">Friday</th>
            <td><input type="time" name="friday-start" value="<?= $autostartConfiguration['Friday_start'] ?>" /></td>
            <td><input type="time" name="friday-end" value="<?= $autostartConfiguration['Friday_end'] ?>" /></td>
        </tr>
        <tr>
            <th class="td-30">Saturday</th>
            <td><input type="time" name="saturday-start" value="<?= $autostartConfiguration['Saturday_start'] ?>" /></td>
            <td><input type="time" name="saturday-end" value="<?= $autostartConfiguration['Saturday_end'] ?>" /></td>
        </tr>
        <tr>
            <th class="td-30">Sunday</th>
            <td><input type="time" name="sunday-start" value="<?= $autostartConfiguration['Sunday_start'] ?>" /></td>
            <td><input type="time" name="sunday-end" value="<?= $autostartConfiguration['Sunday_end'] ?>" /></td>
        </tr>
        <?php
        if ($autostartDevicePresenceEnabled == 'enabled') {
            echo '<tr><td colspan="100%"><p class="yellowtext">Autostart and stop on device presence is enabled. It will overwrite this configuration.</p></td></tr>';
        } ?>
    </table>
    <br>
    <button type="submit" class="btn-small-green">Save</button>
</form>

<br>
<h4>Autostart on device presence</h4>
<div class="flex align-item-center column-gap-4">
    <p>Enable autostart on device presence on the local network</p>
    <label class="onoff-switch-label">
        <input class="onoff-switch-input" type="checkbox" id="enable-device-presence-btn" <?php echo ($autostartDevicePresenceEnabled == 'enabled') ? 'checked' : ''?>>
        <span class="onoff-switch-slider"></span>
    </label>
</div>

<?php
if ($autostartDevicePresenceEnabled == 'enabled') : ?>
    <br>
    <p>
    - Motion will be started if none of the configured devices are present on the local network.<br>
    - Motion will be stopped if at least <b>1</b> of the configured devices is connected to the local network.
    </p>

    <?php
    if (!empty($autostartKnownDevices)) : ?>
        <br>
        <p><b>Known devices</b></p>
        <table>
            <?php
            foreach ($autostartKnownDevices as $knownDevice) :
                $deviceId = $knownDevice['Id'];
                $deviceName = $knownDevice['Name'];
                $deviceIp = $knownDevice['Ip']; ?>
                <tr class="td-fit">
                    <td>
                        <b><?= $deviceName ?></b>
                    </td>
                    <td>
                        <?= $deviceIp ?>
                    </td>
                    <td>
                        <img src="assets/icons/delete.svg" class="icon-lowopacity remove-device-btn" device-id="<?= $deviceId ?>" title="Remove device <?= $deviceName ?>" />
                    </td>
                </tr>
                <?php
            endforeach ?>
        </table>
        <hr>
        <?php
    endif ?>
    <br>
    <form id="device-presence-form" autocomplete="off">
        <p>Add a new device:</p>
        <input type="text" name="device-name" placeholder="Device name" required />
        <input type="text" name="device-ip" placeholder="IP address" required />
        <br><br>
        <button type="submit" class="btn-small-green">Add device</button>
    </form>
    <?php
endif ?>
<br>
<br>

<?php
$content = ob_get_clean();
$slidePanelName = 'autostart';
$slidePanelTitle = 'CONFIGURE AUTOSTART';

include(ROOT . '/views/includes/slide-panel.inc.php');