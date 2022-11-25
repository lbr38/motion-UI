<div class="main-container">

    <h1>MOTION CONFIGURATION</h1>

    <div id="motion-configuration-div">
        <?php
        $configurationFiles = glob('/etc/motion/*.conf');
        /**
         *  Set the main configuration file as first member of the array, to be displayed first
         */
        $configurationFiles = \Controllers\Common::arrayReorder($configurationFiles, '/etc/motion/motion.conf');

        if (!empty($configurationFiles)) :
            foreach ($configurationFiles as $configurationFile) :
                $eventRegistering = '';
                $cameraId = '';
                $cameraName = '';

                /**
                 *  Keep only the filename (and not the entire path)
                 */
                $configurationFile = basename($configurationFile);

                if (is_readable('/etc/motion/' . $configurationFile)) {
                    /**
                     *  Get file content
                     */
                    $contentArray = file('/etc/motion/' . $configurationFile);
                    $contentArray = str_replace('; ', ';', $contentArray);

                    /**
                     *  Check if event registering can be set up for this file.
                     *  The file must at least contain the 'camera_id' parameter.
                     */
                    $cameraId = preg_grep('/camera_id/i', $contentArray);
                    if (!empty($cameraId)) {
                        $eventRegistering = true;
                        $cameraId = array_values(preg_grep('/camera_id/i', $contentArray));
                        $cameraId = trim(str_replace('camera_id', '', $cameraId[0]));
                    }

                    $cameraName = preg_grep('/camera_name/i', $contentArray);
                    if (!empty($cameraName)) {
                        $cameraName = array_values(preg_grep('/camera_name/i', $contentArray));
                        $cameraName = trim(str_replace('camera_name', '', $cameraName[0]));
                    }
                } ?>

                <div class="motion-conf-file-container div-generic-blue">
                    <div>
                        <div class="motion-file-name">
                            <input type="text" class="rename-motion-conf-input" filename="<?= $configurationFile ?>" placeholder="Rename <?= $configurationFile ?>" value="<?= $configurationFile ?>">
                        </div>

                        <div class="motion-file-info-btns-container">
                            <div class="motion-file-info">
                                <?php
                                if ($configurationFile == 'motion.conf') {
                                    echo '<span class="lowopacity">Main configuration file</span><br>';
                                }

                                if (!empty($cameraId)) {
                                    echo '<span class="lowopacity">Camera Id: ' . $cameraId . '</span><br>';
                                }

                                if (!empty($cameraName)) {
                                    echo '<span class="lowopacity">Camera name: ' . $cameraName . '</span><br>';
                                }

                                /**
                                 *  Check that config file is readable and writable
                                 */
                                if (!is_readable('/etc/motion/' . $configurationFile)) {
                                    echo '<span class="yellowtext"><img src="resources/icons/warning.png" class="icon" />File is not readable</span><br>';
                                }
                                if (!is_writable('/etc/motion/' . $configurationFile)) {
                                    echo '<span class="yellowtext"><img src="resources/icons/warning.png" class="icon" />File is not writable</span>';
                                } ?>
                            </div>

                            <div class="motion-file-btns">
                                <div>
                                    <?php
                                    if ($eventRegistering == true) : ?>
                                        <div class="slide-btn setup-event-motion-conf-btn" title="Set up event registering" filename="<?= $configurationFile ?>">
                                            <img src="resources/icons/cog.svg" />
                                            <span>Set up event registering</span>
                                        </div>
                                        <?php
                                    endif ?>

                                    <div class="slide-btn duplicate-motion-conf-btn" title="Duplicate file" filename="<?= $configurationFile ?>">
                                        <img src="resources/icons/duplicate.svg" />
                                        <span>Duplicate file</span>
                                    </div>

                                    <div class="slide-btn save-motion-conf-btn" title="Save file" filename="<?= $configurationFile ?>">
                                        <img src="resources/icons/save.svg" />
                                        <span>Save file</span>
                                    </div>

                                    <div class="slide-btn-red delete-motion-conf-btn" title="Delete file" filename="<?= $configurationFile ?>">
                                        <img src="resources/icons/bin.svg" />
                                        <span>Delete file</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="slide-btn show-motion-conf-btn" title="Show/hide configuration" filename="<?= $configurationFile ?>">
                                        <img src="resources/icons/search.svg" />
                                        <span>Show/hide configuration</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="motion-file-configuration hide" filename="<?= $configurationFile ?>">
                        <?php
                        if ($eventRegistering != true) : ?>
                            <p class="yellowtext">Cannot set up event registering because there is no <b>camera_id</b> parameter in this file.</p><br>
                            <?php
                        endif ?>

                        <form class="motion-configuration-form" filename="<?= $configurationFile ?>" autocomplete="off">
                            <table class="motion-configuration-table">
                                <tr>
                                    <th>E / D</th>
                                    <th>Parameter</th>
                                    <th>Value</th>
                                </tr>

                                <?php
                                $i = 0;

                                if (!empty($contentArray)) :
                                    foreach ($contentArray as $line) :
                                        /**
                                         *  If the line is a comment then ignore it
                                         */
                                        if (preg_match('/^#/', $line)) {
                                            continue;
                                        }

                                        /**
                                         *  Parse the line to separate parameter from its value
                                         *  Parameter is then set on [0] and its value on [1]
                                         */
                                        $line = explode(' ', $line, 2);

                                        /**
                                         *  If line is empty then ignore it
                                         */
                                        if (empty($line[0]) or empty($line[1])) {
                                            continue;
                                        }

                                        $optionName = trim($line[0]);
                                        $optionValue = trim($line[1]);

                                        /**
                                         *  If parameter starts with ';' then is is disabled
                                         *  For the printing of the parameter, the ';' is being removed
                                         */
                                        if (preg_match('/^;/', $optionName)) {
                                            $status = 'disabled';
                                            $optionName = str_replace(';', '', $optionName);
                                        } else {
                                            $status = 'enabled';
                                        }

                                        /**
                                         *  If the parameter contains a '#' then it is a comment in the file.
                                         *  Ignoring it.
                                         */
                                        if (preg_match('/^#/', $optionName)) {
                                            continue;
                                        } ?>

                                        <tr>
                                            <td class="td-fit">
                                                <label class="onoff-switch-label">
                                                    <input class="onoff-switch-input" type="checkbox" name="option-status" option-id="<?= $i ?>" value="enabled" <?php echo ($status == 'enabled') ? 'checked' : ''?>>
                                                    <span class="onoff-switch-slider"></span>
                                                </label>
                                            </td>
                                            <th class="td-10">
                                                <input type="hidden" name="option-name" option-id="<?= $i ?>" value="<?= $optionName ?>" />
                                                <?= $optionName ?>
                                            </th>
                                            <td>
                                                <input type="text" name="option-value" option-id="<?= $i ?>" value="<?= $optionValue ?>" />
                                            </td>
                                        </tr>
                                        <?php
                                        ++$i;
                                    endforeach;
                                endif; ?>

                                <tr>
                                    <td colspan="3">
                                        Add an additonnal parameter:
                                    </td>
                                </tr>
                                </tr>
                                <tr>
                                    <td class="td-fit">
                                        <label class="onoff-switch-label">
                                            <input class="onoff-switch-input" type="checkbox" name="option-status" option-id="<?= $i ?>" value="enabled" checked>
                                            <span class="onoff-switch-slider"></span>
                                        </label>
                                    </td>
                                    <th class="td-10">
                                        <input type="text" name="option-name" option-id="<?= $i ?>" value="" placeholder="Param name" />
                                    </th>
                                    <td>
                                        <input type="text" name="option-value" option-id="<?= $i ?>" value="" placeholder="Param value" />
                                    </td>
                                </tr>
                            </table>
                            <br>
                        </form>
                    </div>
                </div>
                <?php
            endforeach;
        endif; ?>
    </div>
</div>