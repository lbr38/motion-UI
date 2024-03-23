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
unset($mycamera, $context, $configuration, $username, $password, $headers);

ob_end_flush();

/**
 *  Read distant file (stream)
 */
readfile($url, false);
