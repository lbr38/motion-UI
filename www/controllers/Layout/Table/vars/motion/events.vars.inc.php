<?php
$mymotionEvent = new \Controllers\Motion\Event();
$mycamera = new \Controllers\Camera\Camera();
$mypermission = new \Controllers\User\Permission();
$reloadableTableOffset = 0;

/**
 *  Get date from cookies if there are, else set today's date
 */
if (empty($_COOKIE['event-date']) or $_COOKIE['event-date'] == DATE_YMD) {
    $eventDate = DATE_YMD;
    $eventDateTitle = 'TODAY';
} else {
    $eventDate = $_COOKIE['event-date'];
    $eventDateTitle = $eventDate;
}

/**
 *  Retrieve offset from cookie if exists
 */
if (!empty($_COOKIE['tables/motion/events/offset']) and is_numeric($_COOKIE['tables/motion/events/offset'])) {
    $reloadableTableOffset = $_COOKIE['tables/motion/events/offset'];
}

/**
 *  Get list of done tasks, with offset
 */
$reloadableTableContent = $mymotionEvent->getByDate($eventDate, true, $reloadableTableOffset);

/**
 *  Get list of ALL done tasks, without offset, for the total count
 */
$reloadableTableTotalItems = count($mymotionEvent->getByDate($eventDate));

/**
 *  Count total pages for the pagination
 */
$reloadableTableTotalPages = ceil($reloadableTableTotalItems / 10);

/**
 *  Calculate current page number
 */
$reloadableTableCurrentPage = ceil($reloadableTableOffset / 10) + 1;

/**
 *  If the user is not an admin, get user permissions
 */
if (!IS_ADMIN) {
    $permissions = $mypermission->get($_SESSION['id']);
}

/**
 *  Get total files for the day
 */
$totalFilesCount = $mymotionEvent->getTotalFileByDate($eventDate);
