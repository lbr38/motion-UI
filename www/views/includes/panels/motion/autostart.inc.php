<?php ob_start(); ?>

<h5>AUTOSTART TIME PERIOD</h5>

<p class="note">For each day, define the time period in which <b>motion</b> will be running. Outside of this time period, <b>motion</b> will be stopped.</p>
<br>
<p class="note">Specify <b>Start:</b> <code>--:--</code> and <b>End:</b> <code>--:--</code> if you wish not to run motion on a specific day.</p>
<p class="note">Specify <b>Start:</b> <code>00:00</code> and <b>End:</b> <code>00:00</code> if you wish to run motion <b>24hours a day</b>.</p>

<form id="autostart-conf-form" autocomplete="off">
    <h6 class="required">MONDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="monday-start" value="<?= $autostartConfiguration['Monday_start'] ?>" />
        <input type="time" name="monday-end" value="<?= $autostartConfiguration['Monday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>

    <h6 class="required margin-top-0">TUESDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="tuesday-start" value="<?= $autostartConfiguration['Tuesday_start'] ?>" />
        <input type="time" name="tuesday-end" value="<?= $autostartConfiguration['Tuesday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>

    <h6 class="required margin-top-0">WEDNESDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="wednesday-start" value="<?= $autostartConfiguration['Wednesday_start'] ?>" />
        <input type="time" name="wednesday-end" value="<?= $autostartConfiguration['Wednesday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>

    <h6 class="required margin-top-0">THURSDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="thursday-start" value="<?= $autostartConfiguration['Thursday_start'] ?>" />
        <input type="time" name="thursday-end" value="<?= $autostartConfiguration['Thursday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>

    <h6 class="required margin-top-0">FRIDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="friday-start" value="<?= $autostartConfiguration['Friday_start'] ?>" />
        <input type="time" name="friday-end" value="<?= $autostartConfiguration['Friday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>

    <h6 class="required margin-top-0">SATURDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="saturday-start" value="<?= $autostartConfiguration['Saturday_start'] ?>" />
        <input type="time" name="saturday-end" value="<?= $autostartConfiguration['Saturday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>

    <h6 class="required margin-top-0">SUNDAY</h6>
    <div class="flex column-gap-15">
        <input type="time" name="sunday-start" value="<?= $autostartConfiguration['Sunday_start'] ?>" />
        <input type="time" name="sunday-end" value="<?= $autostartConfiguration['Sunday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note">Start</p>
        <p class="note">End</p>
    </div>
   
    <?php
    if ($autostartDevicePresenceEnabled == 'enabled') {
        echo '<br><div class="flex alig-item-center column-gap-5"><img src="/assets/icons/warning.svg" class="icon-np" /><p class="note">Autostart & stop on device presence is enabled. It will overwrite this configuration.</p></div>';
    } ?>

    <br><br>
    <button type="submit" class="btn-small-green">Save</button>
</form>

<br>
<h5>AUTOSTART ON DEVICE PRESENCE</h5>

<h6>ENABLE AUTOSTART ON DEVICE PRESENCE</h6>
<p class="note">Motion will be started if none of the configured devices are present on the local network.</p>
<p class="note">Motion will be stopped if at least <b>one</b> of the configured devices is connected to the local network.</p>
<label class="onoff-switch-label">
    <input class="onoff-switch-input" type="checkbox" id="enable-device-presence-btn" <?php echo ($autostartDevicePresenceEnabled == 'enabled') ? 'checked' : ''?>>
    <span class="onoff-switch-slider"></span>
</label>

<?php
if ($autostartDevicePresenceEnabled == 'enabled') :
    if (!empty($autostartKnownDevices)) : ?>
        <h6 class="margin-bottom-5">KNOWN DEVICES</h6>

        <?php
        foreach ($autostartKnownDevices as $knownDevice) :
            $deviceId = $knownDevice['Id'];
            $deviceName = $knownDevice['Name'];
            $deviceIp = $knownDevice['Ip']; ?>

            <div class="table-container grid-2 bck-blue-alt">
                <div>
                    <p><?= $deviceName ?></p>
                    <p class="mediumopacity-cst"><?= $deviceIp ?></p>
                </div>
                <div class="flex justify-end">
                    <img src="/assets/icons/delete.svg" class="icon-lowopacity remove-device-btn" device-id="<?= $deviceId ?>" title="Remove device <?= $deviceName ?>" />
                </div>
            </div>
            <?php
        endforeach;
    endif ?>

    <h6>ADD A DEVICE</h6>
    <p class="note">It can be a smartphone, a tablet, etc ...</p>

    <form id="device-presence-form" autocomplete="off">
        <h6 class="required">NAME</h6>
        <p class="note">A name to identify the device.</p>
        <input type="text" name="device-name" placeholder="Device name" required />

        <h6 class="required">IP ADDRESS</h6>
        <p class="note">The IP address of the device on the local network. Example: <code>192.168.0.10</code></p>
        <input type="text" name="device-ip" placeholder="IP address" required />

        <br><br>
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