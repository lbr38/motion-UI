<?php
if (!IS_ADMIN) {
    throw new Exception('You are not allowed to access this page.');
}

/**
 *  Get date start and end from cookies if there are, else set a default interval of 7 days.
 */
if (empty($_COOKIE['statsDateStart'])) {
    $statsDateStart = date('Y-m-d', strtotime('-7 day', strtotime(DATE_YMD)));
} else {
    $statsDateStart = $_COOKIE['statsDateStart'];
}

if (empty($_COOKIE['statsDateEnd'])) {
    $statsDateEnd = DATE_YMD;
} else {
    $statsDateEnd = $_COOKIE['statsDateEnd'];
}
