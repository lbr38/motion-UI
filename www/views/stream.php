<?php

/**
 *  Check that specified camera Id is valid
 */
if (empty($_GET['id'])) {
    return;
}
if (!is_numeric($_GET['id'])) {
    return;
}

/**
 *  URL is go2rtc stream server, followed by camera id
 */
$url = 'http://127.0.0.1:1984/api/stream.mjpeg?src=camera_' . $_GET['id'];

/**
 *  Define context options
 *  Set default socket timeout to 5 seconds
 */
$context = [
    'http' => [
        'timeout' => 5,
        'method' => 'GET'
    ]
];

/**
 *  Set context (will apply to get_headers and readfile)
 */
stream_context_set_default($context);

/**
 *  Get distant URL headers
 */
$headers = get_headers($url, true);

/**
 *  Quit if headers could not be retrieved
 */
if ($headers == false) {
    return;
}

/**
 * Convert headers keys to lowercase
 */
$headers = array_change_key_case($headers);

/**
 *  Set content type
 */
header('Content-Type: ' . $headers['content-type']);

/**
 *  Clear memory
 */
unset($context, $headers);

ob_end_flush();

/**
 *  Read distant file (stream)
 */
readfile($url, false);
