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
 *  Default context: set timeout
 */
$contextArray = [
    'http'=> [
        'timeout' => '1'
    ]
];

/**
 *  Use username and password if not empty
 */
if (!empty($username) and !empty($password)) {
    $contextArray['http']['header'] = 'Authorization: Basic ' . base64_encode($username . ':' . $password);
}

$context = stream_context_create($contextArray);

ob_end_flush();

readfile($url, false, $context);
