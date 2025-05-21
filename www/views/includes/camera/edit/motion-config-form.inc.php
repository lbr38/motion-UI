<form id="camera-motion-settings-form" camera-id="<?= $id ?>">
    <p class="note">Some parameters can be overwritten by the camera settings form. You can lock a parameter to prevent it from being overwritten.</p>

    <input id="camera-motion-settings-search" type="text" placeholder="Search" />

    <?php
    /**
     *  Print each section and its parameters
     */
    foreach ($formParams as $section => $sectionParams) : ?>
        <div class="div-generic-blue bck-blue-alt margin-top-10 margin-bottom-10">
            <div class="flex column-gap-10 camera-motion-settings-form-section-toggle-btn pointer" section="<?= $section ?>">
                <img src="/assets/icons/<?= $sectionParams['icon'] ?>.svg" class="icon-np" />
                <h5 class="margin-top-0 margin-bottom-0"><?= $sectionParams['title'] ?></h5>
            </div>

            <div class="camera-motion-settings-form-section margin-top-20 hide" section="<?= $section ?>">
                <?php
                foreach ($sectionParams['params'] as $param => $attributes) :
                    $editable = '';
                    $required = '';

                    // If a default value is set, use it as default value, otherwise use an empty string as default value
                    if (isset($attributes['default'])) {
                        $value = $attributes['default'];
                    } else {
                        $value = '';
                    }

                    // If the parameter is already set (in database), use its value as default value
                    if (isset($currentMotionConfiguration[$param]['value'])) {
                        $value = $currentMotionConfiguration[$param]['value'];
                    }

                    // If the parameter is not editable, disable the input field
                    if (isset($attributes['editable']) && $attributes['editable'] === false) {
                        $editable = 'disabled';
                    }

                    // If the parameter is required, add the 'required' class
                    if (isset($attributes['required']) && $attributes['required'] === true) {
                        $required = 'required';
                    }

                    // If the parameter is disabled, add some opacity to the container and hide the value container
                    if (!isset($currentMotionConfiguration[$param]['enabled']) or (isset($currentMotionConfiguration[$param]['enabled']) && $currentMotionConfiguration[$param]['enabled'] != 'true')) {
                        $containerClass = 'opacity-60-cst';
                        $paramValueContainerClass = 'hide';
                    } else {
                        $containerClass = '';
                        $paramValueContainerClass = 'flex';
                    } ?>

                    <div class="param-container <?= $containerClass ?>" param-name="<?= $param ?>">
                        <div class="flex align-flex-start column-gap-10 justify-space-between">
                            <div>
                                <div class="flex align-item-center column-gap-10">
                                    <h6 class="margin-top-0 <?= $required ?>"><?= $attributes['title'] ?></h6>
                                    <code><?= $param ?></code>
                                    <a href="https://motion-project.github.io/motionplus_config.html#<?= $param ?>" target="_blank" title="Open official Motion documentation for this parameter"><img src="/assets/icons/external-link.svg" class="icon-small mediumopacity" /></a>
                                </div>

                                <p class="note param-description" param-name="<?= $param ?>" title="<?= $attributes['description'] ?>"><?= $attributes['description'] ?></p>
                            </div>

                            <?php
                            /**
                             *  Print 'Enable' button only if the parameter is editable
                             */
                            if (!isset($attributes['editable']) or $attributes['editable'] === true) : ?>
                                <div class="flex align-item-center column-gap-5" title="Enable/Disable this parameter">
                                    <label class="onoff-switch-label">
                                        <input class="param-enable onoff-switch-input" type="checkbox" param-name="<?= $param ?>" <?= isset($currentMotionConfiguration[$param]['enabled']) && $currentMotionConfiguration[$param]['enabled'] == 'true' ? 'checked' : '' ?>>
                                        <span class="onoff-switch-slider"></span>
                                    </label>
                                </div>
                                <?php
                            endif; ?>
                        </div>

                        <div class="param-value-container <?= $paramValueContainerClass ?> align-item-center justify-space-between column-gap-10" param-name="<?= $param ?>">
                            <div class="flex-div-90">
                                <?php
                                /**
                                 *  Print input field
                                 */

                                // If input type must be a text
                                if ($attributes['type'] == 'text') : ?>
                                    <input class="param-input-value" type="<?= $attributes['type'] ?>" param-name="<?= $param ?>" value="<?= $value ?>" <?= $editable ?> />
                                    <?php
                                endif;

                                // If input type must be a number
                                if ($attributes['type'] == 'number') : ?>
                                    <input class="param-input-value" type="number" param-name="<?= $param ?>" <?= isset($attributes['min']) ? 'min="' . $attributes['min'] . '"' : '' ?> <?= isset($attributes['max']) ? 'max="' . $attributes['max'] . '"' : '' ?> value="<?= $value ?>" <?= $editable ?> />
                                    <?php
                                endif;

                                // If input type must be a on-off switch
                                if ($attributes['type'] == 'switch') : ?>
                                    <label class="onoff-switch-label" title="Set On/Off value for this parameter">
                                        <input class="param-input-value onoff-switch-input" type="checkbox" param-name="<?= $param ?>" <?= $value == 'on' ? 'checked' : '' ?> <?= $editable ?>>
                                        <span class="onoff-switch-slider"></span>
                                    </label>
                                    <?php
                                endif;

                                // If input must be a range
                                if ($attributes['type'] == 'range') : ?>
                                    <div class="flex align-item-center column-gap-10">
                                        <input class="param-input-value width-100" type="range" param-name="<?= $param ?>" <?= isset($attributes['min']) ? 'min="' . $attributes['min'] . '"' : '' ?> <?= isset($attributes['max']) ? 'max="' . $attributes['max'] . '"' : '' ?> <?= isset($attributes['step']) ? 'step="' . $attributes['step'] . '"' : '' ?> value="<?= $value ?>" <?= $editable ?> oninput="this.nextElementSibling.value = this.value" />
                                        <output><?= $value ?></output>
                                    </div>
                                    <?php
                                endif;

                                // If input must be a select
                                if ($attributes['type'] == 'select') : ?>
                                    <select class="param-input-value" param-name="<?= $param ?>" <?= $editable ?>>
                                        <?php
                                        // Print each possible options, and if the current value is in the list, select it
                                        foreach ($attributes['options'] as $option) :
                                            if (isset($option['description']) and isset($option['value'])) {
                                                $text = $option['description'] . ' (' . $option['value'] . ')';
                                            } else {
                                                $text = $option['value'];
                                            } ?>
                                            <option value="<?= $option['value'] ?>" <?= $value == $option['value'] ? 'selected' : '' ?>><?= $text ?></option>
                                            <?php
                                        endforeach;

                                        // If the current value is not in the list of options, add it to the list and select it
                                        if (!in_array($value, array_column($attributes['options'], 'value'))) : ?>
                                            <option value="<?= $value ?>" selected><?= $value ?></option>
                                            <?php
                                        endif ?>
                                    </select>
                                    <?php
                                endif ?>
                            </div>

                            <div title="Lock/Unlock parameter to prevent overwriting">
                                <?php
                                /**
                                 *  Print lock/unlock icon if the parameter is editable
                                 */
                                if (!isset($attributes['editable']) or (isset($attributes['editable']) and $attributes['editable'] === true)) :
                                    if (isset($currentMotionConfiguration[$param]['locked']) && $currentMotionConfiguration[$param]['locked'] == 'true') : ?>
                                        <img class="param-lock icon pointer" src="/assets/icons/locked.svg" param-name="<?= $param ?>" value="true" />
                                        <?php
                                    else : ?>
                                        <img class="param-lock icon pointer" src="/assets/icons/unlocked.svg" param-name="<?= $param ?>" value="false" />
                                        <?php
                                    endif;
                                endif; ?>
                            </div>
                        </div>

                        <hr class="margin-top-20 margin-bottom-20">
                    </div>

                    <?php
                endforeach ?>
            </div>
        </div>
        <?php
    endforeach ?>

    <br>
    <button type="submit" class="btn-small-green">Save</button>
</form>

<br><br>

<script>
    selectToSelect2('select.param-input-value[param-name="netcam_url"]', 'Select or specify a stream URL...', true, false);
    selectToSelect2('select.param-input-value[param-name="netcam_high_url"]', 'Select or specify a stream URL...', true, false);
</script>
