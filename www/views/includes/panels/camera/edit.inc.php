<?php ob_start(); ?>

<div id="camera-edit-form-container">
    <?php include_once(ROOT . '/views/includes/camera/edit/form.inc.php'); ?>
</div>

<?php
$content = ob_get_clean();
$slidePanelName = 'camera/edit';
$slidePanelTitle = strtoupper($cameraRawParams['name']) . ' SETTINGS';

include(ROOT . '/views/includes/slide-panel.inc.php');
