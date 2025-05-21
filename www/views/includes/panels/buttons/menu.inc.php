<?php ob_start(); ?>

<div class="flex flex-direction-column row-gap-20">
    <?php
    if (IS_ADMIN) : ?>
        <div class="flex column-gap-10 get-panel-btn pointer mediumopacity" panel="general/notification">
            <img src="/assets/icons/alarm.svg" class="icon" />
            <h5 class="margin-0">NOTIFICATIONS <?php echo (NOTIFICATION > 0) ? '(' . NOTIFICATION . ')' : ''?></h5>
        </div>
        <?php
    endif ?>

    <div class="flex column-gap-10 get-panel-btn pointer mediumopacity" panel="general/user/userspace">
        <img src="/assets/icons/user.svg" class="icon" />
        <h5 class="margin-0">USERSPACE</h5>
    </div>

    <?php
    if (IS_ADMIN) : ?>
        <div class="flex column-gap-10 get-panel-btn pointer mediumopacity" panel="general/logs">
            <img src="/assets/icons/file.svg" class="icon" />
            <h5 class="margin-0">LOGS</h5>
        </div>

        <div class="flex column-gap-10 get-panel-btn pointer mediumopacity" panel="general/settings">
            <img src="/assets/icons/cog.svg" class="icon" />
            <h5 class="margin-0">SETTINGS</h5>
        </div>
        <?php
    endif ?>
</div>

<?php
$content = ob_get_clean();
$slidePanelName = 'buttons/menu';
$slidePanelTitle = '';

include(ROOT . '/views/includes/slide-panel.inc.php');
