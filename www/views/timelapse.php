<?php

/**
 *  Check that specified media Id is valid
 */
if (empty($_GET['id'])) {
    return;
}
if (empty($_GET['picture'])) {
    return;
}

$filePath = CAMERAS_TIMELAPSE_DIR . '/camera-' . $_GET['id'] . '/' . $_GET['picture'];

if (!file_exists($filePath)) {
    return;
}

ob_end_flush();

readfile($filePath);
