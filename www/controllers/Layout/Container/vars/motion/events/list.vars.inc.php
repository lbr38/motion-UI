<?php
$motionEventController = new \Controllers\Motion\Event();

// Get total unseen events
$unseenEventsTotal = $motionEventController->getUnseenCount();

unset($motionEventController);
