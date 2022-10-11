<div id="motion-autostart-container" class="item">
    <h3>Motion: autostart</h3>

    <?php
    if ($motionAutostartEnabled == "disabled") : ?>
        <button id="enable-autostart-btn" class="btn-square-green" title="Enable motion service autostart">
            <img src="resources/icons/time.svg" class="icon" />
        </button>
        <span class="block center lowopacity">Enable and configure autostart</span>
        <?php
    endif;
    if ($motionAutostartEnabled == "enabled") : ?>
        <button id="disable-autostart-btn" class="btn-square-red" title="Disable motion service autostart">
            <img src="resources/icons/time.svg" class="icon" />
        </button>
        <span class="block center lowopacity">Disable autostart</span>
        <?php
    endif ?>

    <br>

    <div id="autostart-btn-div">
        <?php
        if ($motionAutostartEnabled == "enabled") : ?>
            <span id="configure-autostart-btn" class="btn-medium-green">Configure autostart</span>
            <?php
        endif ?>
    </div>

    <?php
    if ($motionAutostartEnabled == "enabled") : ?>
        <div id="autostart-div" class="config-div hide">
            <p class="center">Configure autostart time slots:</p>
            <br>
            <form id="autostart-conf-form" autocomplete="off">
                <table id="motion-alert-table">
                    <tr>
                        <th class="td-30"></th>
                        <th>Start</th>
                        <th>End</th>
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
                        echo '<tr><td colspan="100%"><p class="yellowtext">Info: Autostart and stop on device presence is enabled. It will overwrite this configuration.</p></td></tr>';
                    }
                    ?>
                </table>
                <br>
                <button type="submit" class="btn-small-green">Save</button>
            </form>
            <br>
                    
            <span>Enable autostart on device presence on the local network </span>
            <label class="onoff-switch-label">
                <input class="onoff-switch-input" type="checkbox" id="enable-device-presence-btn" <?php echo ($autostartDevicePresenceEnabled == 'enabled') ? 'checked' : ''?>>
                <span class="onoff-switch-slider"></span>
            </label>
                    
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
                        foreach ($autostartKnownDevices as $knownDevice) {
                            $deviceId = $knownDevice['Id'];
                            $deviceName = $knownDevice['Name'];
                            $deviceIp = $knownDevice['Ip'];
                            echo '<tr class="td-fit"><td><b>' . $deviceName . '</b></td><td>' . $deviceIp . '</td><td><img src="resources/icons/bin.svg" class="icon-lowopacity remove-device-btn" device-id="' . $deviceId . '" title="Remove device ' . $deviceName . '" /></td></tr>';
                        } ?>
                    </table>
                    <hr>
                <?php endif ?>
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
        </div>

        <?php
    endif ?>
</div>