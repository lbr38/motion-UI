<?php
/**
 *  Pseudo slide panel
 *  Content is filled with JS
 */
ob_start(); ?> 

<div id="camera-edit-form-container"></div>

<?php
$content = ob_get_clean();
$slidePanelName = 'edit-camera';
$slidePanelTitle = 'EDIT CAMERA';

include(ROOT . '/views/includes/slide-panel.inc.php');