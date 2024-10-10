<?php
/**
 *  Pseudo slide panel
 *  Content is filled with JS
 */
ob_start(); ?> 

<div id="camera-edit-motion-config-form-container"></div>

<?php
$content = ob_get_clean();
$slidePanelName = 'edit-motion-config';
$slidePanelTitle = 'EDIT MOTION CONFIGURATION';

include(ROOT . '/views/includes/slide-panel.inc.php');