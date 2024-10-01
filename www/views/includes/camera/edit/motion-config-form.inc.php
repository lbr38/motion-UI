<form id="camera-motion-settings-form" camera-id="<?= $id ?>">
    <?php
    $eventRegistering = false;

    // Generate camera motion configuration file if not exist
    $mymotionConfig->generateCameraConfig($id);

    // Retrieve the filename (and not the entire path)
    $configurationFile = basename(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf'); ?>

    <h6>MOTION CONFIGURATION</h6>
    <span class="lowopacity-cst">All available parameters can be found in the </span><span><a target="_blank" href="https://motion-project.github.io/motion_config.html#Configuration_OptionsAlpha">official Motion documentation<img src="/assets/icons/external-link.svg" class="icon" /></a></span>

    <?php
    $params = $mymotionConfig->getConfig(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf');

    /**
     *  Check if event registering can be set up for this file.
     *  The params must at least contain the 'camera_id' parameter.
     */
    if (!empty($params['camera_id'])) {
        $eventRegistering = true;
    }

    if ($eventRegistering !== true) {
        echo '<p class="yellowtext">Cannot set up event registering because there is no <b>camera_id</b> parameter in this file.</p><br>';
    } ?>

    <br><br>

    <p class="yellowtext">Be careful when manually editing motion parameters as it could break motion / motion-UI.</p>
    <p class="yellowtext"><br>Note: setting <code>picture_output</code> param to <code>on</code> is not recommended as it could save a large amount of pictures and slow down motion-UI interface when printing events medias.</p>
    
    <div class="grid grid-fr-auto-1-2 align-item-center column-gap-10 row-gap-10 margin-top-20">
        <span>E / D</span>
        <span>Parameter</span>
        <span>Value</span>
    </div>

    <?php
    $i = 0;

    if (!empty($params)) :
        foreach ($params as $param => $details) :
            $status = $details['status'];
            $value = $details['value']; ?>

            <div class="grid grid-fr-auto-1-2 align-item-center column-gap-10 row-gap-10">
                <!-- Param status -->
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input" type="checkbox" name="option-status" option-id="<?= $i ?>" value="enabled" <?php echo ($status == 'enabled') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>

                <!-- Param name -->
                <span name="option-name" option-id="<?= $i ?>" value="<?= $param ?>"><?= $param ?></span>

                <!-- Param value -->
                <input type="text" name="option-value" option-id="<?= $i ?>" value="<?= $value ?>" />
            </div>
            <?php
            ++$i;
        endforeach;
    endif ?>
                
    <div class="margin-top-10">
        <p>Add an additional parameter:</p>
    </div>
    
    <div class="grid grid-fr-auto-1-2 align-item-center column-gap-10 row-gap-10">
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