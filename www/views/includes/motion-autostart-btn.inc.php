<div id="motion-autostart-container" class="item">
    <p class="center"><b>Motion: autostart</b></p>

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
            <div id="configure-autostart-btn" class="slide-btn" title="Configure autostart">
                <img src="resources/icons/cog.svg" />
                <span>Configure autostart</span>
            </div>
            <?php
        endif ?>
    </div>
</div>