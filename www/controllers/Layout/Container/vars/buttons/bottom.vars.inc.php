<?php

$mymotionEvent = new \Controllers\Motion\Event();

/**
 *  Get total unseen events
 */
$unseenEventsTotal = $mymotionEvent->getUnseenCount();
