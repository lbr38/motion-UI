<div class="param-slide-container camera-configuration-div" camera-id="<?= $camera['Id'] ?>">
    <div class="param-slide">
        <div id="camera-settings-container" camera-id="<?= $camera['Id'] ?>">
            <img src="resources/icons/error-close.svg" class="hide-camera-configuration-btn close-btn pointer lowopacity" title="Close" camera-id="<?= $camera['Id'] ?>" />

            <h2 class="center">Configure <?= $camera['Name'] ?> camera</h2>
            
            <h2>Global settings</h2>

            <form id="camera-global-settings-form" camera-id="<?= $camera['Id'] ?>" output-type="<?= $camera['Output_type'] ?>" autocomplete="off">

                <span>Output type</span>
                <span><?= $camera['Output_type'] ?></span>

                <span>Name</span>
                <input type="text" name="edit-camera-name" value="<?= $camera['Name'] ?>" />

                <span>URL</span>
                <input type="text" name="edit-camera-url" value="<?= $camera['Url'] ?>" />

                <?php
                if ($camera['Output_type'] == 'image') : ?>
                    <span class="camera-refresh-field">Refresh image (sec.)</span>
                    <input class="camera-refresh-field" type="number" name="edit-camera-refresh" value="<?= $camera['Refresh'] ?>" />
                    <?php
                endif; ?>

                <span>Rotate</span>
                <select name="edit-camera-rotate">
                    <option value="0" <?php echo $camera['Rotate'] == "0" ? 'selected' : '' ?>>0</option>
                    <!-- <option value="90" <?php //echo $camera['Rotate'] == "90" ? 'selected' : '' ?>>90</option> -->
                    <option value="180" <?php echo $camera['Rotate'] == "180" ? 'selected' : '' ?>>180</option>
                    <!-- <option value="270" <?php //echo $camera['Rotate'] == "270" ? 'selected' : '' ?>>270</option> -->
                </select>

                <span>Display camera live stream</span>
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input" type="checkbox" name="edit-camera-live-enable" <?php echo $camera['Live_enabled'] == "true" ? 'checked' : '' ?>>
                    <span class="onoff-switch-slider"></span>
                </label>

                <span>Enable motion detection</span>
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input" type="checkbox" name="edit-camera-motion-enable" camera-id="<?= $camera['Id'] ?>" <?php echo $camera['Motion_enabled'] == "true" ? 'checked' : '' ?>>
                    <span class="onoff-switch-slider"></span>
                </label>

                <?php
                /**
                 *  Hide stream URL field if camera output type is video or if motion detection is disabled
                 */
                $hideField = '';
                if ($camera['Output_type'] == 'video') {
                    $hideField = 'hide';
                }
                if ($camera['Output_type'] == 'image' and $camera['Motion_enabled'] == 'false') {
                    $hideField = 'hide';
                } ?>

                <br>
                <div>
                    <p class="camera-stream-url yellowtext <?= $hideField ?>">Motion detection cannot work on static images. Specify a stream URL to use for Motion detection:</p>
                </div>
                <span class="camera-stream-url <?= $hideField ?>">Stream URL</span>
                <input class="camera-stream-url <?= $hideField ?>" type="text" name="edit-camera-stream-url" placeholder="e.g. http(s)://.../stream" value="<?= $camera['Stream_url'] ?>" />

                <p><b>HTTP Authentication</b></p>
                <span></span>

                <span>Username</span>
                <input type="text" name="edit-camera-username" value="<?= $camera['Username'] ?>" />

                <span>Password</span>
                <input type="password" name="edit-camera-password" value="<?= $camera['Password'] ?>" />
            
                <br>
                <button type="submit" class="btn-small-green">Save</button>
            </form>

            <br>

            <?php
            if ($camera['Motion_enabled'] == 'true') :
                echo '<h2>Motion detection settings</h2>';

                $eventRegistering = false;

                /**
                 *  Generate camera motion configuration file if not exist
                 */
                $mycamera->generateMotionConfiguration($camera['Id']);

                /**
                 *  Keep only the filename (and not the entire path)
                 */
                $configurationFile = basename(CAMERAS_DIR . '/camera-' . $camera['Id'] . '.conf');

                if (!is_readable(CAMERAS_DIR . '/' . $configurationFile)) :
                    echo '<p class="yellowtext">Cannot read configuration file</p>';
                else :
                    /**
                     *  Get file content
                     */
                    $contentArray = file(CAMERAS_DIR . '/' . $configurationFile);
                    $contentArray = str_replace('; ', ';', $contentArray);

                    /**
                     *  Check if event registering can be set up for this file.
                     *  The file must at least contain the 'camera_id' parameter.
                     */
                    if (!empty(preg_grep('/camera_id/i', $contentArray))) {
                        $eventRegistering = true;
                    } ?>

                    <div class="camera-motion-settings" camera-id="<?= $camera['Id'] ?>">
                        <?php
                        if ($eventRegistering != true) : ?>
                            <p class="yellowtext">Cannot set up event registering because there is no <b>camera_id</b> parameter in this file.</p><br>
                            <?php
                        endif ?>

                        <form class="camera-motion-settings-form" camera-id="<?= $camera['Id'] ?>" autocomplete="off">                
                            <div class="flex align-item-center column-gap-4">
                                <label class="onoff-switch-label">
                                    <input id="motion-advanced-edition-mode" class="onoff-switch-input settings-param" type="checkbox" <?php echo ($settings['Motion_advanced_edition_mode'] == 'true') ? 'checked' : ''?>>
                                    <span class="onoff-switch-slider"></span>
                                </label>
                                <span>Advanced edition mode</span>
                            </div>

                            <div id="advanced-edition-mode-warning" <?php echo ($settings['Motion_advanced_edition_mode'] != 'true') ? 'class="hide"' : ''?>>
                                <br>
                                <p class="yellowtext">Be careful when manually editing motion parameters as it could break motion / motion-UI.</p>
                                <br>
                                <p>All available parameters can be found in the <a target="_blank" href="https://motion-project.github.io/motion_config.html#Configuration_OptionsAlpha">official Motion documentation<img src="resources/icons/external-link.svg" class="icon" /></a></p>
                            </div>

                            <p class="yellowtext"><br>Note: setting <b>picture_output</b> param to <b>on</b> is not recommended as it could save a large amount of pictures and slow down motion-UI interface when printing events medias.</p>

                            <table class="motion-configuration-table">
                                <tr>
                                    <th>E / D</th>
                                    <th>Parameter</th>
                                    <th>Value</th>
                                </tr>

                                <?php
                                $i = 0;

                                if (!empty($contentArray)) :
                                    /**
                                     *  Set row class depending on advanced edition mode
                                     */
                                    if ($settings['Motion_advanced_edition_mode'] == 'true') {
                                        $rowClass = 'advanced-param';
                                    } else {
                                        $rowClass = 'advanced-param hide';
                                    }

                                    /**
                                     *  Default masked params and values
                                     */
                                    $hiddenParams = array(
                                        'camera_id',
                                        'camera_name',
                                        'videodevice',
                                        'netcam_url',
                                        'netcam_keepalive',
                                        'netcam_tolerant_check',
                                        'netcam_high_url',
                                        'netcam_userpass',
                                        'v4l2_palette',
                                        'picture_filename',
                                        'movie_filename',
                                        'on_event_start',
                                        'on_event_end',
                                        'on_movie_end',
                                        'on_picture_save',
                                        'pre_capture',
                                        'post_capture',
                                        'picture_type',
                                        'movie_codec',
                                        'movie_quality',
                                        'movie_bps',
                                        'movie_max_time'
                                    );

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
                                        }

                                        /**
                                         *  If parameter is a member of $hiddenParams[] then don't print it
                                         */
                                        if (in_array($optionName, $hiddenParams)) : ?>
                                            <tr class="<?= $rowClass ?>">
                                            <?php
                                        else : ?>
                                            <tr>
                                            <?php
                                        endif ?>
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
                                endif ?>

                                <tr class="<?= $rowClass ?>">
                                    <td colspan="3">
                                        <br>Add an additonnal parameter:<br><br>
                                    </td>
                                </tr>
                    
                                <tr class="<?= $rowClass ?>">
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
                            <button type="submit" class="btn-small-green">Save</button>
                            <br><br>
                        </form>
                    </div>
                    <?php
                endif;
            endif ?>
        </div>
    </div>
</div>