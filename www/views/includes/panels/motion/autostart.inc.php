<?php ob_start(); ?>

<h5><?= __('AUTOSTART ON DEVICE PRESENCE') ?></h5>

<h6><?= __('ENABLE AUTOSTART ON DEVICE PRESENCE') ?></h6>
<p class="note">● <?= __('Motion detection will be started if none of the configured devices are present on the local network.') ?></p>
<p class="note">● <?= __('Motion detection will be stopped if at least <b>one</b> of the configured devices is connected to the local network.') ?></p>
<label class="onoff-switch-label">
    <input class="onoff-switch-input" type="checkbox" id="enable-device-presence-btn" <?php echo ($autostartDevicePresenceEnabled == 'enabled') ? 'checked' : ''?>>
    <span class="onoff-switch-slider"></span>
</label>

<?php
if ($autostartDevicePresenceEnabled == 'enabled') :
    if (!empty($autostartKnownDevices)) : ?>
        <h6 class="margin-bottom-5"><?= __('KNOWN DEVICES') ?></h6>

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
                    <img src="/assets/icons/delete.svg" class="icon-lowopacity remove-device-btn" device-id="<?= $deviceId ?>" title="<?= sprintf(__('Remove device %s'), $deviceName) ?>" />
                </div>
            </div>
            <?php
        endforeach;
    endif ?>

    <h6><?= __('ADD A DEVICE') ?></h6>
    <p class="note"><?= __('It can be a smartphone, a tablet, etc ...') ?></p>

    <form id="device-presence-form" autocomplete="off">
        <h6 class="required"><?= __('NAME') ?></h6>
        <p class="note"><?= __('A name to identify the device.') ?></p>
        <input type="text" name="device-name" placeholder="<?= __('e.g. Alice smartphone') ?>" required />

        <h6 class="required"><?= __('IP ADDRESS') ?></h6>
        <p class="note"><?= __('The IP address of the device on the local network.') ?></p>
        <input type="text" name="device-ip" placeholder="<?= __('e.g. 192.168.0.10') ?>" required />

        <br><br>
        <button type="submit" class="btn-small-green"><?= __('Add') ?></button>
    </form>
    <?php
endif ?>

<hr class="margin-top-40">

<h5><?= __('AUTOSTART TIME PERIOD') ?></h5>

<?php
if ($autostartDevicePresenceEnabled == 'enabled') {
    echo '<div class="flex align-item-center column-gap-10"><img src="/assets/icons/warning.svg" class="icon-np" /><p class="note">' . __('Autostart on device presence is enabled. Time period will be prioritized if current time is in the time period.') . '</p></div><br>';
} ?>

<?php
if ($autostartDevicePresenceEnabled == 'enabled') {
    echo '<p class="note">' . __('For each day, define the time period in which <b>motion</b> will be running. Outside of this time period, device presence will take over.') . '</p>';
} else {
    echo '<p class="note">' . __('For each day, define the time period in which <b>motion</b> will be running. Outside of this time period, <b>motion</b> will be stopped.') . '</p>';
} ?>

<br>
<p class="note">● <?= __('Specify <b>Start:</b> <code>--:--</code> and <b>End:</b> <code>--:--</code> if you wish not to run motion on a specific day.') ?></p>
<p class="note">● <?= __('Specify <b>Start:</b> <code>00:00</code> and <b>End:</b> <code>00:00</code> if you wish to run motion <b>24hours a day</b>.') ?></p>

<form id="autostart-conf-form" autocomplete="off">
    <h6 class="required"><?= __('MONDAY') ?></h6>
    <div class="flex column-gap-15">
        <input type="time" name="monday-start" value="<?= $autostartConfiguration['Monday_start'] ?>" />
        <input type="time" name="monday-end" value="<?= $autostartConfiguration['Monday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note"><?= _x('Start', 'time period') ?></p>
        <p class="note"><?= _x('End', 'time period') ?></p>
    </div>

    <h6 class="required margin-top-0"><?= __('TUESDAY') ?></h6>
    <div class="flex column-gap-15">
        <input type="time" name="tuesday-start" value="<?= $autostartConfiguration['Tuesday_start'] ?>" />
        <input type="time" name="tuesday-end" value="<?= $autostartConfiguration['Tuesday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note"><?= _x('Start', 'time period') ?></p>
        <p class="note"><?= _x('End', 'time period') ?></p>
    </div>

    <h6 class="required margin-top-0"><?= __('WEDNESDAY') ?></h6>
    <div class="flex column-gap-15">
        <input type="time" name="wednesday-start" value="<?= $autostartConfiguration['Wednesday_start'] ?>" />
        <input type="time" name="wednesday-end" value="<?= $autostartConfiguration['Wednesday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note"><?= _x('Start', 'time period') ?></p>
        <p class="note"><?= _x('End', 'time period') ?></p>
    </div>

    <h6 class="required margin-top-0"><?= __('THURSDAY') ?></h6>
    <div class="flex column-gap-15">
        <input type="time" name="thursday-start" value="<?= $autostartConfiguration['Thursday_start'] ?>" />
        <input type="time" name="thursday-end" value="<?= $autostartConfiguration['Thursday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note"><?= _x('Start', 'time period') ?></p>
        <p class="note"><?= _x('End', 'time period') ?></p>
    </div>

    <h6 class="required margin-top-0"><?= __('FRIDAY') ?></h6>
    <div class="flex column-gap-15">
        <input type="time" name="friday-start" value="<?= $autostartConfiguration['Friday_start'] ?>" />
        <input type="time" name="friday-end" value="<?= $autostartConfiguration['Friday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note"><?= _x('Start', 'time period') ?></p>
        <p class="note"><?= _x('End', 'time period') ?></p>
    </div>

    <h6 class="required margin-top-0"><?= __('SATURDAY') ?></h6>
    <div class="flex column-gap-15">
        <input type="time" name="saturday-start" value="<?= $autostartConfiguration['Saturday_start'] ?>" />
        <input type="time" name="saturday-end" value="<?= $autostartConfiguration['Saturday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note"><?= _x('Start', 'time period') ?></p>
        <p class="note"><?= _x('End', 'time period') ?></p>
    </div>

    <h6 class="required margin-top-0"><?= __('SUNDAY') ?></h6>
    <div class="flex column-gap-15">
        <input type="time" name="sunday-start" value="<?= $autostartConfiguration['Sunday_start'] ?>" />
        <input type="time" name="sunday-end" value="<?= $autostartConfiguration['Sunday_end'] ?>" />
    </div>
    <div class="flex column-gap-15 justify-space-around">
        <p class="note"><?= _x('Start', 'time period') ?></p>
        <p class="note"><?= _x('End', 'time period') ?></p>
    </div>

    <br>
    <button type="submit" class="btn-small-green"><?= __('Save') ?></button>
</form>

<br>
<br>

<?php
$content = ob_get_clean();
$slidePanelName = 'motion/autostart';
$slidePanelTitle = __('CONFIGURE AUTOSTART');

include(ROOT . '/views/includes/slide-panel.inc.php');