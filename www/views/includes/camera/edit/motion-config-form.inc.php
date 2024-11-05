<form id="camera-motion-settings-form" camera-id="<?= $id ?>">
    <?php
    $eventRegistering = false;

    // Generate camera motion configuration file if not exist
    $mymotionConfig->generateCameraConfig($id);

    // Retrieve the filename (and not the entire path)
    $configurationFile = basename(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf'); ?>

    <p class="note">All available parameters can be found in the <a target="_blank" href="https://motion-project.github.io/motion_config.html#Configuration_OptionsAlpha">official Motion documentation <img src="/assets/icons/external-link.svg" class="icon" /></a></p>

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
        echo '<div class="flex column-gap-5"><img src="/assets/icons/warning.svg" class="icon-np" /><p class="note">Cannot set up event registering because there is no <b>camera_id</b> parameter in this file.</p></div>';
    } ?>

    <br>
    <p class="note">Be careful when manually editing motion parameters as it could break motion / motion-UI.</p>

    <h6>CURRENT PARAMETERS</h6>

    <?php
    $i = 0;

    if (!empty($params)) :
        foreach ($params as $param => $details) :
            $status = $details['status'];
            $value = $details['value']; ?>

            <div class="table-container grid-fr-4-1 bck-blue-alt pointer motion-param-collapse-btn" param-id="<?= $i ?>">
                <div>
                    <p><?= $param ?></p>
                    <p class="lowopacity-cst"><?= $value ?></p>
                </div>

                <div class="flex justify-end">
                    <img src="/assets/icons/close.svg" class="icon-lowopacity motion-param-delete-btn" title="Delete parameter <?= $param ?>" camera-id="<?= $id ?>" param-name="<?= $param ?>" />
                </div>
            </div>

            <div class="motion-param-div details-div margin-bottom-10 hide" param-id="<?= $i ?>">
                <!-- Param name -->
                <h6 class="margin-top-0">NAME</h6>
                <span name="param-name" param-id="<?= $i ?>" value="<?= $param ?>"><code><?= $param ?></code></span>

                <!-- Param value -->
                <h6 class="required">VALUE</h6>
                <input type="text" name="param-value" param-id="<?= $i ?>" value="<?= $value ?>" />

                <!-- Param status -->
                <h6 class="required">ENABLE</h6>
                <label class="onoff-switch-label">
                    <input class="onoff-switch-input" type="checkbox" name="param-status" param-id="<?= $i ?>" value="enabled" <?php echo ($status == 'enabled') ? 'checked' : ''?>>
                    <span class="onoff-switch-slider"></span>
                </label>
            </div>
            <?php
            ++$i;
        endforeach;
    endif ?>

    <h6>ADDITIONAL PARAMETER</h5>

    <h6 class="required">NAME</h6>
    <input type="text" name="additional-param-name" value="" placeholder="Parameter name" />

    <h6 class="required">VALUE</h6>
    <input type="text" name="additional-param-value" value="" placeholder="Parameter value" />

    <h6 class="required">ENABLE</h6>
    <label class="onoff-switch-label">
        <input class="onoff-switch-input" type="checkbox" name="additional-param-status" value="enabled" checked>
        <span class="onoff-switch-slider"></span>
    </label>
    
    <br><br>
    <button type="submit" class="btn-small-green">Save</button>
</form>

<br><br>