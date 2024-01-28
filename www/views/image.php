<?php

$mycamera = new \Controllers\Camera();

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

// Possible support for /dev/videoX
// If URL stars with /dev/videoX, use passthru
// if (preg_match('#^/dev/video[0-9]+$#', $url)) {
//     passthru('/usr/bin/ffmpeg -f video4linux2 -i /dev/video0 -s 1920x1080 pipe:.jpg 2>/dev/null');
//     return;
// }

/**
 *  Read distant file (stream)
 */
readfile($url, false);
