<h4>Global settings</h4>

<p class="lowopacity-cst">These settings are applied both to the live stream and to the motion detection.</p>
<br>

<form id="camera-global-settings-form" camera-id="<?= $camera['Id'] ?>" output-type="<?= $camera['Output_type'] ?>" autocomplete="off">
    <div class="margin-left-15">
        <div class="grid grid-fr-1-2 align-item-center column-gap-10 row-gap-10">
            <span>Id</span>
            <span>#<?= $camera['Id'] ?></span>

            <span>Output type</span>
            <p><span class="label-blue"><?= $camera['Output_type'] ?></span></p>

            <span>Name</span>
            <input type="text" name="edit-camera-name" value="<?= $camera['Name'] ?>" />
            
            <span>URL</span>
            <input type="text" name="edit-camera-url" value="<?= $camera['Url'] ?>" />
    
            <span>Output resolution</span>
            <select name="edit-output-resolution">
                <!-- 4/3 -->
                <option disabled>4/3 resolutions:</option>
                <option value="640x480" <?php echo ($camera['Output_resolution'] == '640x480') ? 'selected' : ''; ?>>640x480</option>
                <option value="800x600" <?php echo ($camera['Output_resolution'] == '800x600') ? 'selected' : ''; ?>>800x600</option>
                <option value="960x720" <?php echo ($camera['Output_resolution'] == '960x720') ? 'selected' : ''; ?>>960x720</option>
                <option value="1024x768" <?php echo ($camera['Output_resolution'] == '1024x768') ? 'selected' : ''; ?>>1024x768</option>
                <option value="1152x864" <?php echo ($camera['Output_resolution'] == '1152x864') ? 'selected' : ''; ?>>1152x864</option>
                <option value="1280x960" <?php echo ($camera['Output_resolution'] == '1280x960') ? 'selected' : ''; ?>>1280x960</option>
                <option value="1400x1050" <?php echo ($camera['Output_resolution'] == '1400x1050') ? 'selected' : ''; ?>>1400x1050</option>
                <option value="1440x1080" <?php echo ($camera['Output_resolution'] == '1440x1080') ? 'selected' : ''; ?>>1440x1080</option>
                <option value="1600x1200" <?php echo ($camera['Output_resolution'] == '1600x1200') ? 'selected' : ''; ?>>1600x1200</option>
                <option value="1856x1392" <?php echo ($camera['Output_resolution'] == '1856x1392') ? 'selected' : ''; ?>>1856x1392</option>
                <option value="1920x1440" <?php echo ($camera['Output_resolution'] == '1920x1440') ? 'selected' : ''; ?>>1920x1440</option>
                <option value="2048x1536" <?php echo ($camera['Output_resolution'] == '2048x1536') ? 'selected' : ''; ?>>2048x1536</option>
                <!-- 16/9 -->
                <option disabled>16/9 resolutions:</option>
                <option value="1280x720" <?php echo ($camera['Output_resolution'] == '1280x720') ? 'selected' : ''; ?>>1280x720 (720p)</option>
                <option value="1920x1080" <?php echo ($camera['Output_resolution'] == '1920x1080') ? 'selected' : ''; ?>>1920x1080 (1080p)</option>
                <option value="2560x1440" <?php echo ($camera['Output_resolution'] == '2560x1440') ? 'selected' : ''; ?>>2560x1440 (1440p)</option>
                <option value="3840x2160" <?php echo ($camera['Output_resolution'] == '3840x2160') ? 'selected' : ''; ?>>3840x2160 (2160p)</option>
                <option value="5120x2880" <?php echo ($camera['Output_resolution'] == '5120x2880') ? 'selected' : ''; ?>>5120x2880 (2880p)</option>
                <option value="7680x4320" <?php echo ($camera['Output_resolution'] == '7680x4320') ? 'selected' : ''; ?>>7680x4320 (4320p)</option>
            </select>

            <span>Rotate</span>
            <select name="edit-camera-rotate">
                <option value="0" <?php echo $camera['Rotate'] == "0" ? 'selected' : '' ?>>0</option>
                <option value="180" <?php echo $camera['Rotate'] == "180" ? 'selected' : '' ?>>180</option>
            </select>

            <span>Text left</span>
            <input type="text" name="edit-camera-text-left" value="<?= $camera['Text_left'] ?>" />

            <span>Text right</span>
            <input type="text" name="edit-camera-text-right" value="<?= $camera['Text_right'] ?>" />
        </div>

        <p class="camera-stream-url hide yellowtext margin-bottom-10">Motion detection cannot work on static images. Specify a stream URL to use for Motion detection:</p>
    
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
    
        <div class="grid grid-fr-1-2 align-item-center column-gap-10 row-gap-10">
            <span class="camera-stream-url <?= $hideField ?>">Stream URL</span>
            <input class="camera-stream-url <?= $hideField ?>" type="text" name="edit-camera-stream-url" placeholder="e.g. http(s)://.../stream or rtsp://..." value="<?= $camera['Stream_url'] ?>" />
        </div>
            
        <p class="margin-top-15 margin-bottom-10">HTTP Authentication</p>

        <p class="lowopacity-cst margin-bottom-5">Be aware that credentials will be stored in plain text in the database as camera authentication is using <code>Basic</code> HTTP Authentication.</p>
        
        <div class="grid grid-fr-1-2 align-item-center column-gap-10 row-gap-10">
            <span>Username</span>
            <input type="text" name="edit-camera-username" value="<?= $camera['Username'] ?>" />
            
            <span>Password</span>
            <input type="password" name="edit-camera-password" value="<?= $camera['Password'] ?>" />
        </div>

        <div class="grid grid-fr-1-2 align-item-center column-gap-10 row-gap-10 margin-top-15">
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

            <span>Enable timelapse</span>
            <label class="onoff-switch-label">
                <input class="onoff-switch-input" type="checkbox" name="edit-camera-timelapse-enable" camera-id="<?= $camera['Id'] ?>" <?php echo $camera['Timelapse_enabled'] == "true" ? 'checked' : '' ?>>
                <span class="onoff-switch-slider"></span>
            </label>
        </div>

        <div class="flex column-gap-10 margin-top-20">
            <button type="submit" class="btn-small-green">Save</button>
            <button type="button" class="btn-small-red delete-camera-btn" title="Delete camera" camera-id="<?= $camera['Id'] ?>">Delete</button>
        </div>
    </div>
</form>

<br>

<?php
if ($camera['Live_enabled'] == "true") : ?>
    <hr>
    <h4>Live stream settings</h4>

    <p class="lowopacity-cst">These settings are applied only to the live stream.</p>
    <br>

    <form id="camera-stream-settings-form" camera-id="<?= $camera['Id'] ?>" autocomplete="off">
        <div class="margin-left-15">
            <div class="grid grid-fr-1-2 align-item-center column-gap-10 row-gap-10">
                <?php
                /**
                 *  Print refresh field only if camera output type is image
                 */
                if ($camera['Output_type'] == 'image') : ?>
                    <span>Refresh stream image (sec.)</span>
                    <input type="number" name="camera-stream-setting-refresh" value="<?= $camera['Refresh'] ?>" />
                    <?php
                endif ?>

                <span>Timestamp left</span>
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input" type="checkbox" name="camera-stream-setting-timestamp-left" <?php echo $camera['Timestamp_left'] == "true" ? 'checked' : '' ?>>
                    <span class="onoff-switch-slider"></span>
                </label>

                <span>Timestamp right</span>
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input" type="checkbox" name="camera-stream-setting-timestamp-right" <?php echo $camera['Timestamp_right'] == "true" ? 'checked' : '' ?>>
                    <span class="onoff-switch-slider"></span>
                </label>
            </div>

            <br>
            <button type="submit" class="btn-small-green">Save</button>
        </div>
    </form>
    <br>
    <?php
endif ?>

<?php
if ($camera['Motion_enabled'] == 'true') : ?>
    <hr>
    <h4>Motion detection settings</h4>

    <p class="lowopacity-cst">These settings are applied only to the motion detection.</p>
    <br>

    <?php

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
                        <input id="motion-advanced-edition-mode" class="onoff-switch-input settings-param" type="checkbox" camera-id="<?= $camera['Id'] ?>" <?php echo (MOTION_ADVANCED_EDITION_MODE === true) ? 'checked' : ''?>>
                        <span class="onoff-switch-slider"></span>
                    </label>
                    <span>Advanced edition mode</span>
                </div>

                <div id="advanced-edition-mode-warning" <?php echo (MOTION_ADVANCED_EDITION_MODE !== true) ? 'class="hide"' : ''?>>
                    <br>
                    <p class="yellowtext">Be careful when manually editing motion parameters as it could break motion / motion-UI.</p>
                    <br>
                    <p>All available parameters can be found in the <a target="_blank" href="https://motion-project.github.io/motion_config.html#Configuration_OptionsAlpha">official Motion documentation<img src="/assets/icons/external-link.svg" class="icon" /></a></p>
                </div>

                <p class="yellowtext"><br>Note: setting <code>picture_output</code> param to <code>on</code> is not recommended as it could save a large amount of pictures and slow down motion-UI interface when printing events medias.</p>
                
                <div class="grid grid-fr-auto-1-2 align-item-center column-gap-10 row-gap-10 margin-top-20">
                    <span>E / D</span>
                    <span>Parameter</span>
                    <span>Value</span>
                </div>

                <?php
                $i = 0;
                if (!empty($contentArray)) :
                    /**
                     *  Set row class depending on advanced edition mode
                     */
                    if (MOTION_ADVANCED_EDITION_MODE === true) {
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
                            <div class="grid grid-fr-auto-1-2 align-item-center column-gap-10 row-gap-10 <?= $rowClass ?>">
                            <?php
                        else : ?>
                            <div class="grid grid-fr-auto-1-2 align-item-center column-gap-10 row-gap-10">
                            <?php
                        endif ?>
                            <label class="onoff-switch-label">
                                <input class="onoff-switch-input" type="checkbox" name="option-status" option-id="<?= $i ?>" value="enabled" <?php echo ($status == 'enabled') ? 'checked' : ''?>>
                                <span class="onoff-switch-slider"></span>
                            </label>

                            <span name="option-name" option-id="<?= $i ?>" value="<?= $optionName ?>"><?= $optionName ?></span>

                            <input type="text" name="option-value" option-id="<?= $i ?>" value="<?= $optionValue ?>" />
                        </div>
                        <?php
                        ++$i;
                    endforeach;
                endif ?>
                    
                <div class="<?= $rowClass ?> margin-top-10">
                    <p>Add an additional parameter:</p>
                </div>
        
                <div class="grid grid-fr-auto-1-2 align-item-center column-gap-10 row-gap-10 <?= $rowClass ?>">
                    <label class="onoff-switch-label">
                        <input class="onoff-switch-input" type="checkbox" name="additional-option-status" value="enabled" checked>
                        <span class="onoff-switch-slider"></span>
                    </label>

                    <input type="text" name="additional-option-name" value="" placeholder="Param name" />
                    <input type="text" name="additional-option-value" value="" placeholder="Param value" />
                </div>

                <br>
                <button type="submit" class="btn-small-green">Save</button>
            </form>
        </div>
        <?php
    endif;
endif ?>
<br>
<br>