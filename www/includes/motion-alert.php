<div id="motion-alert-container" class="item">
    <h3>Motion: alerts</h3>

    <?php
    if ($alertEnabled == "disabled") {
        echo '<button type="button" id="enable-alert-btn" class="btn-square-green" title="Enable motion alerts"><img src="resources/icons/alarm.svg" class="icon"></button>';
        echo '<span class="block center lowopacity">Enable and configure alerts</span>';
    }
    if ($alertEnabled == "enabled") {
        echo '<button type="button" id="disable-alert-btn" class="btn-square-red" title="Disable motion alerts"><img src="resources/icons/alarm.svg" class="icon"></button>';
        echo '<span class="block center lowopacity">Disable alerts</span>';
    } ?>

    <br>

    <div id="alert-btn-div">
        <?php
        if ($alertEnabled == "enabled") {
            echo '<span id="configure-alerts-btn" class="btn-small-green">Configure alerts</span>';
            echo '<button type="button" id="how-to-alert-btn" class="btn-medium-yellow">How to</button>';
        } ?>
    </div>

    <?php
    if ($alertEnabled == "enabled") : ?>
        <div id="alert-div" class="config-div hide">
            <p class="center">Configure alerts time slots:</p>
            <br>

            <form id="alert-conf-form" autocomplete="off">
                <table id="motion-alert-table">
                    <tr>
                        <th class="td-30"></th>
                        <th>Start</th>
                        <th>End</th>
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
                    <tr title="Specify mail alert recipient, separated by a comma.">
                        <th class="td-10">Mail recipient</th>
                        <td colspan="2"><input type="email" name="mail-recipient" value="<?= $alertConfiguration['Recipient'] ?>" required />
                    </tr>
                    <tr title="(optional) Specify mutt configuration file path to use.">
                        <th class="td-10">Mutt config path</th>
                        <td colspan="2"><input type="text" name="mutt-config" value="<?= $alertConfiguration['Mutt_config'] ?>" />
                    </tr>

                    <?php
                    /**
                     *  Display a warning if event script does not exists
                     */
                    if (!file_exists(DATA_DIR . '/tools/event')) {
                        echo '<tr><td colspan="100%"><p class="yellowtext">The event script <b>' . DATA_DIR . '/tools/event</b> is not found. You may configure alerts but you will not receive them.</p></td></tr>';
                    }

                    /**
                     *  Display a warning if mutt config file is not readable or not exist
                     */
                    if (!empty($alertConfiguration['Mutt_config'])) {
                        if (!file_exists($alertConfiguration['Mutt_config'])) {
                            echo '<tr><td colspan="100%"><p class="yellowtext">The specified mutt config file <b>' . $alertConfiguration['Mutt_config'] . '</b> is not found. You may configure alerts but you will not receive them.</p></td></tr>';
                        }
                        if (!is_readable($alertConfiguration['Mutt_config'])) {
                            echo '<tr><td colspan="100%"><p class="yellowtext">The specified mutt config file <b>' . $alertConfiguration['Mutt_config'] . '</b> is not readable. You may configure alerts but you will not receive them.</p></td></tr>';
                        }
                    } ?>
                </table>
                <br>
                <button type="submit" class="btn-small-green">Save</button>
            
                <br>
            </form>
        </div>
        <?php
    endif ?>
</div>