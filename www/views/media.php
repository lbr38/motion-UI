<?php

$mymotionEvent = new \Controllers\Motion\Event();

/**
 *  Check that specified media Id is valid
 */
if (empty($_GET['id'])) {
    http_response_code(400);
    error_log("Invalid media ID.");
    return;
}
if (!is_numeric($_GET['id'])) {
    http_response_code(400);
    error_log("Invalid media ID.");
    return;
}

$filePath = $mymotionEvent->getFilePath($_GET['id']);

/**
 *  If file is not found
 */
if (empty($filePath)) {
    http_response_code(404);
    error_log("File not found: " . $_GET['id']);
    return;
}

/**
 *  If the requested file must be a thumbnail
 */
if (isset($_GET['thumbnail'])) {
    $filePath .= '.thumbnail.jpg';
}

/**
 *  Check if the file exists and is readable
 */
if (!file_exists($filePath) || !is_readable($filePath)) {
    http_response_code(404);
    error_log("File not found or not readable: " . $filePath);
    return;
}

/**
 *  Set the appropriate Content-Type header
 */
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$contentType = finfo_file($finfo, $filePath);
finfo_close($finfo);

header('Content-Type: ' . $contentType);
header('Content-Length: ' . filesize($filePath));

ob_end_flush();

/**
 *  Read and output the file
 */
if (readfile($filePath, false) === false) {
    http_response_code(500);
    echo "Failed to read file.";
    error_log("Failed to read file: " . $filePath);
}
