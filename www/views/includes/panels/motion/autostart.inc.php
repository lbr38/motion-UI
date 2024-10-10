<?php ob_start(); ?>

<h4>Autostart time period</h4>

<p class="input-note">For each day, define the time period in which <b>motion</b> will be running. Outside of this time period, <b>motion</b> will be stopped.</p>
<br>
<p class="input-note">Specify <b>Start:</b> <code>--:--</code> and <b>End:</b> <code>--:--</code> if you wish not to run motion on a specific day.</p>
<br>
<p class="input-note">Specify <b>Start:</b> <code>00:00</code> and <b>End:</b> <code>00:00</code> if you wish to run motion <b>24hours a day</b>.</p>
<br>

<form id="autostart-conf-form" autocomplete="off">
    <h6>MONDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="monday-start" value="<?= $autostartConfiguration['Monday_start'] ?>" />
        <input type="time" name="monday-end" value="<?= $autostartConfiguration['Monday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <h6>TUESDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="tuesday-start" value="<?= $autostartConfiguration['Tuesday_start'] ?>" />
        <input type="time" name="tuesday-end" value="<?= $autostartConfiguration['Tuesday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <h6>WEDNESDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="wednesday-start" value="<?= $autostartConfiguration['Wednesday_start'] ?>" />
        <input type="time" name="wednesday-end" value="<?= $autostartConfiguration['Wednesday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <h6>THURSDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="thursday-start" value="<?= $autostartConfiguration['Thursday_start'] ?>" />
        <input type="time" name="thursday-end" value="<?= $autostartConfiguration['Thursday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <h6>FRIDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="friday-start" value="<?= $autostartConfiguration['Friday_start'] ?>" />
        <input type="time" name="friday-end" value="<?= $autostartConfiguration['Friday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <h6>SATURDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="saturday-start" value="<?= $autostartConfiguration['Saturday_start'] ?>" />
        <input type="time" name="saturday-end" value="<?= $autostartConfiguration['Saturday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <h6>SUNDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="sunday-start" value="<?= $autostartConfiguration['Sunday_start'] ?>" />
        <input type="time" name="sunday-end" value="<?= $autostartConfiguration['Sunday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="input-note">Start</p>
        <p class="input-note">End</p>
    </div>

    <br>
    
    <?php
    if ($autostartDevicePresenceEnabled == 'enabled') {
        echo '<br><p class="yellowtext">Autostart & stop on device presence is enabled. It will overwrite this configuration.</p>';
    } ?>

    <br>
    <button type="submit" class="btn-small-green">Save</button>
</form>

<br>
<h4>Autostart on device presence</h4>
<div class="flex align-item-center column-gap-5">
    <p>Enable autostart on device presence on the local network</p>
    <label class="onoff-switch-label">
        <input class="onoff-switch-input" type="checkbox" id="enable-device-presence-btn" <?php echo ($autostartDevicePresenceEnabled == 'enabled') ? 'checked' : ''?>>
        <span class="onoff-switch-slider"></span>
    </label>
</div>

<?php
if ($autostartDevicePresenceEnabled == 'enabled') : ?>
    <br>
    <p class="input-note">Motion will be started if none of the configured devices are present on the local network.</p>
    <br>
    <p class="input-note">Motion will be stopped if at least <b>one</b> of the configured devices is connected to the local network.</p>

    <?php
    if (!empty($autostartKnownDevices)) : ?>
        <h6>KNOWN DEVICES</h6>

        <table>
            <?php
            foreach ($autostartKnownDevices as $knownDevice) :
                $deviceId = $knownDevice['Id'];
                $deviceName = $knownDevice['Name'];
                $deviceIp = $knownDevice['Ip']; ?>
                <tr class="td-fit">
                    <td>
                        <?= $deviceName ?>
                    </td>
                    <td>
                        <?= $deviceIp ?>
                    </td>
                    <td>
                        <img src="/assets/icons/delete.svg" class="icon-lowopacity remove-device-btn" device-id="<?= $deviceId ?>" title="Remove device <?= $deviceName ?>" />
                    </td>
                </tr>
                <?php
            endforeach ?>
        </table>
        <?php
    endif ?>
    <br>

    <form id="device-presence-form" autocomplete="off">
        <h6>ADD A DEVICE</h6>
        <div class="flex column-gap-15">
            <input type="text" name="device-name" placeholder="Device name" required />
            <input type="text" name="device-ip" placeholder="IP address" required />
        </div>
        <br>
        <button type="submit" class="btn-small-green">Add</button>
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