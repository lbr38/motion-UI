<div id="motion-alert-container" class="item">
    <p class="center"><b>Motion: alerts</b></p>

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
        if ($alertEnabled == "enabled") : ?>
            <div id="configure-alerts-btn" class="slide-btn" title="Configure alerts">
                <img src="resources/icons/cog.svg" />
                <span>Configure alerts</span>
            </div>
            <?php
        endif ?>
    </div>
</div>