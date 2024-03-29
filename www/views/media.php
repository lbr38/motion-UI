<?php

$mymotionEvent = new \Controllers\Motion\Event();

/**
 *  Check that specified media Id is valid
 */
if (empty($_GET['id'])) {
    return;
}
if (!is_numeric($_GET['id'])) {
    return;
}

$filePath = $mymotionEvent->getFilePath($_GET['id']);

/**
 *  If file is not found
 */
if (empty($filePath)) {
    return;
}

ob_end_flush();

/**
 *  If the requested file must be a thumbnail
 */
if (isset($_GET['thumbnail'])) {
    readfile($filePath . '.thumbnail.jpg');
} else {
    readfile($filePath);
}
