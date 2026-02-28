<?php
$mymotionEvent = new \Controllers\Motion\Event();
$mycamera = new \Controllers\Camera\Camera();
$mypermission = new \Controllers\User\Permission();
$reloadableTableOffset = 0;

// Get date from cookies if there are, else set today's date
if (empty($_COOKIE['event-date']) or $_COOKIE['event-date'] == DATE_YMD) {
    $eventDate = DATE_YMD;
    $eventDateTitle = 'TODAY';
} else {
    $eventDate = $_COOKIE['event-date'];
    $eventDateTitle = $eventDate;
}

// If there are cameras filter in cookies, get them as an array, else get all cameras ids as an array
if (!empty($_COOKIE['tmp/events-filter-cameras'])) {
    $cameras = explode(',', $_COOKIE['tmp/events-filter-cameras']);
} else {
    $cameras = $mycamera->getCamerasIds();
}

// Retrieve offset from cookie if exists
if (!empty($_COOKIE['tables/motion/events/offset']) and is_numeric($_COOKIE['tables/motion/events/offset'])) {
    $reloadableTableOffset = $_COOKIE['tables/motion/events/offset'];
}

// Get list of events, with offset
$reloadableTableContent = $mymotionEvent->getByDateAndCamera($eventDate, $cameras, true, $reloadableTableOffset);

// Get list of ALL events, without offset, for the total count
$reloadableTableTotalItems = count($mymotionEvent->getByDateAndCamera($eventDate, $cameras));

// Get total files count for the current date and cameras filter
$totalFilesCount = count($mymotionEvent->getFilesByDateAndCamera($eventDate, $cameras));

// Count total pages for the pagination
$reloadableTableTotalPages = ceil($reloadableTableTotalItems / 10);

// Calculate current page number
$reloadableTableCurrentPage = ceil($reloadableTableOffset / 10) + 1;

// If the user is not an admin, get user permissions
if (!IS_ADMIN) {
    $permissions = $mypermission->get($_SESSION['id']);
}

unset($mypermission);
