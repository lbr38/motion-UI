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
            'main-stream' => [
                'device' => '',
                'resolution' => '640x360',
                'width' => '640',
                'height' => '360',
                'framerate' => 25,
                'rotate' => '0',
                'text-left' => '',
                'text-right' => '',
                'timestamp-left' => '',
                'timestamp-right' => '',
            ],
            'secondary-stream' => [
                'device' => '',
                'resolution' => '640x360',
                'width' => '640',
                'height' => '360',
                'framerate' => 25,
            ],
            'authentication' => [
                'username' => '',
                'password' => '',
            ],
            'stream' => [
                'enable' => 'true',
                'technology' => 'mse'
            ],
            'motion-detection' => [
                'enable' => 'true'
            ],
            'timelapse' => [
                'enable' => 'false'
            ],
            'hardware-acceleration' => 'false',
            'onvif' => [
                'enable' => 'false',
                'port' => '80',
                'uri' => '',
                'url' => '',
            ],
        ];
    }
}
