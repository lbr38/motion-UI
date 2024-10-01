<?php

$mycamera = new \Controllers\Camera\Camera();

/**
 *  Check that specified camera Id is valid
 */
if (empty($_GET['id'])) {
    return;
}
if (!is_numeric($_GET['id'])) {
    return;
}
if (!$mycamera->existId($_GET['id'])) {
    return;
}

/**
 *  Get camera configuration
 */
$configuration = $mycamera->getConfiguration($_GET['id']);
$url = $configuration['Url'];
$username = $configuration['Username'];
$password = $configuration['Password'];

/**
 *  Define context options
 *  Set default socket timeout to 3 seconds
 */
$context = [
    'http' => [
        'timeout' => 3,
        'method' => 'GET'
    ]
];

/**
 *  Append username and password if not empty
 *  Convert to base64
 */
if (!empty($username) and !empty($password)) {
    $context['http']['header'] = 'Authorization: Basic ' . base64_encode($username . ':' . $password);
}

/**
 *  Set context (will apply to readfile)
 */
stream_context_set_default($context);

/**
 *  Clear memory
 */
unset($mycamera, $context, $configuration, $username, $password);

ob_end_flush();

// Support for /dev/videoX and rtsp:// streams
// If URL stars with /dev/videoX or rtsp://
if (preg_match('#^/dev/video[0-9]+$#', $url) or preg_match('#^rtsp://#', $url)) {
    /**
     *  Read local output file
     */
    readfile('/var/lib/motionui/cameras/camera-' . $_GET['id'] . '/output/output.jpg', false);
    return;
}

/**
 *  Read distant file (stream)
 */
readfile($url, false);
