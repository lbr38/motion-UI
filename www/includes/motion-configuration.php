<div id="motion-configuration-div">

    <h2>Motion: configuration</h2>

    <div id="configuration-container">

        <?php
        $configurationFiles = glob('/etc/motion/*.conf');
        /**
         *  Set the main configuration file as first member of the array, to be displayed first
         */
        $configurationFiles = \Controllers\Common::arrayReorder($configurationFiles, '/etc/motion/motion.conf');

        if (!empty($configurationFiles)) :
            foreach ($configurationFiles as $configurationFile) :
                /**
                 *  Keep only the filename (and not the entire path)
                 */
                $configurationFile = basename($configurationFile); ?>

                <div>
                    <div class="center">
                        <input type="text" class="input-large center rename-motion-conf-input" filename="<?= $configurationFile ?>" placeholder="Rename <?= $configurationFile ?>" value="<?= $configurationFile ?>">
                        
                        <?php
                        if ($configurationFile == 'motion.conf') {
                            echo '<br><span class="lowopacity">(main configuration file)</span>';
                        }
                        ?>

                        <?php
                        /**
                         *  Check that config file is readable and writable
                         */
                        if (!is_readable('/etc/motion/' . $configurationFile)) {
                            echo '<span class="yellowtext"><img src="resources/icons/warning.png" class="icon" />File not readable</span>';
                        }
                        if (!is_writable('/etc/motion/' . $configurationFile)) {
                            echo '<span class="yellowtext"><img src="resources/icons/warning.png" class="icon" />File not writable</span>';
                        }
                        ?>

                        <br><br>

                        <span class="btn-small-green show-motion-conf-btn" filename="<?= $configurationFile ?>">Show/Hide</span>
                        <span class="btn-small-green duplicate-motion-conf-btn" filename="<?= $configurationFile ?>">Duplicate</span>
                        <span class="btn-xsmall-red delete-motion-conf-btn" filename="<?= $configurationFile ?>">Delete</span>
                    </div>

                    <br>

                    <div class="config-div motion-conf-div" filename="<?= $configurationFile ?>">
                        <form class="motion-configuration-form" filename="<?= $configurationFile ?>" autocomplete="off">
                            <table class="motion-configuration-table">
                                <tr>
                                    <th>E / D</th>
                                    <th>Parameter</th>
                                    <th>Value</th>
                                </tr>

                                <?php
                                $content = file('/etc/motion/' . $configurationFile);
                                $content = str_replace('; ', ';', $content);
                                $i = 0;

                                foreach ($content as $line) :
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

                                    $optionName = $line[0];
                                    $optionValue = $line[1];

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
                                endforeach ?>
                            </table>
                            <br>

                            <button type="submit" class="btn-small-green">Save</button>

                        </form>
                    </div>
                </div>
                <?php
            endforeach;
        endif; ?>
    </div>
</div>