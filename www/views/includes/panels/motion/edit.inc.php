<?php ob_start(); ?> 

<div id="camera-edit-motion-config-form-container">
    <?php include_once(ROOT . '/views/includes/camera/edit/motion-config-form.inc.php'); ?>
</div>

<?php
$content = ob_get_clean();
$slidePanelName = 'motion/edit';
$slidePanelTitle = 'EDIT MOTION CONFIGURATION';

include(ROOT . '/views/includes/slide-panel.inc.php');
