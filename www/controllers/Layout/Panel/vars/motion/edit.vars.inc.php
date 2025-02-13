<?php
if (!IS_ADMIN) {
    throw new Exception('You are not allowed to access this panel.');
}

$mymotionConfig = new \Controllers\Motion\Config();
$mycamera = new \Controllers\Camera\Camera();

/**
 *  Check if camera exists
 */
if ($mycamera->existId($item['id']) === false) {
    throw new Exception('Camera does not exist');
}

$id = $item['id'];
