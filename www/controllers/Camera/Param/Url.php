<?php

namespace Controllers\Camera\Param;

use Exception;

class Url
{
    /**
     *  Check that URL is valid
     */
    public static function check(string $url) : void
    {
        /**
         *  Check that URL is not empty
         */
        if (empty($url)) {
            throw new Exception('URL or device is required');
        }

        /**
         *  Check that URL starts with http(s)://, rtsp:// or /dev/video
         */
        if (!preg_match('#(^https?://|^rtsp://|^/dev/video)#', $url)) {
            throw new Exception('URL must start with <b>http(s)://</b>, <b>rtsp://</b> or <b>/dev/video</b>');
        }

        /**
         *  Check that URL does not contain invalid characters
         */
        if (str_contains($url, "'") || str_contains($url, '"') || str_contains($url, "\\") || str_contains($url, '<') || str_contains($url, '>')) {
            throw new Exception('Url contains invalid characters');
        }
    }
}
