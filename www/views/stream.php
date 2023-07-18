<?php

$mycamera = new \Controllers\Camera();
$contextArray = array();

/**
 *  Set default socket timeout to 2 seconds
 *  Will apply to get_headers, readfile...
 */
stream_context_set_default(
    array(
        'http' => array(
            'timeout' => 2
        )
    )
);

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
 *  Use username and password if not empty
 */
if (!empty($username) and !empty($password)) {
    $contextArray['http']['header'] = 'Authorization: Basic ' . base64_encode($username . ':' . $password);
}

$context = stream_context_create($contextArray);

ob_end_flush();

readfile($url, false, $context);
