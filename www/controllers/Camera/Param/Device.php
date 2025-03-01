<?php

namespace Controllers\Camera\Param;

use Exception;

class Device
{
    /**
     *  Check that device/URL is valid
     */
    public static function check(string $url, bool $required = true) : void
    {
        if (!$required && empty($url)) {
            return;
        }

        /**
         *  Check that device/URL is not empty
         */
        if (empty($url)) {
            throw new Exception('Device or URL is required');
        }

        /**
         *  Check that URL starts with http(s)://, rtsp:// or /dev/video
         */
        if (!preg_match('#(^https?://|^rtsp://|^/dev/video)#', $url)) {
            throw new Exception('Device or URL must start with <b>http(s)://</b>, <b>rtsp://</b> or <b>/dev/video</b>');
        }

        /**
         *  Check that URL does not contain invalid characters
         */
        if (str_contains($url, "'") || str_contains($url, '"') || str_contains($url, "\\") || str_contains($url, '<') || str_contains($url, '>')) {
            throw new Exception('Device or URL contains invalid characters');
        }
    }
}
