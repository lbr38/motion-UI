<section class="main-container reloadable-container" container="getting-started">
    <?php
    if ($cameraTotal == 0) : ?>
        <h4 class="center"><?= __('GETTING STARTED') ?></h4>
        <p class="center"><?= sprintf(__('Go to the %s tab to add a camera'), '<img src="/assets/icons/videocam.svg" class="icon" />') ?></p>
        <?php
    endif ?>
</section>