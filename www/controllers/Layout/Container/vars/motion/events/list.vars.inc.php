<?php

$mymotionEvent = new \Controllers\Motion\Event();
$mycamera = new \Controllers\Camera\Camera();

/**
 *  Get total unseen events
 */
$unseenEventsTotal = $mymotionEvent->getUnseenCount();
