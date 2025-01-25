<?php

namespace Controllers\Camera;

use Exception;

class Config
{
    /**
     *  Return camera configuration template
     */
    public function getTemplate()
    {
        return [
            'name' => '',
            'url' => '',
            'width' => '',
            'height' => '',
            'framerate' => '',
            'rotate' => '0',
            'text-left' => '',
            'text-right' => '',
            'timestamp-left' => '',
            'timestamp-right' => '',
            'basic-auth-username' => '',
            'basic-auth-password' => '',
            'stream-enable' => 'true',
            'motion-detection-enable' => 'true',
            'timelapse-enable' => 'false',
            'hardware-acceleration' => 'false',
        ];
    }
}
