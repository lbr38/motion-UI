<?php
if (!IS_ADMIN) {
    throw new Exception('You are not allowed to access this panel.');
}

$mymotionAlert = new \Controllers\Motion\Alert();

$alertConfiguration = $mymotionAlert->getConfiguration();
