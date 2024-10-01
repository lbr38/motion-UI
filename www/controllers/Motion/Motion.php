<?php

namespace Controllers\Motion;

use Exception;

class Motion
{
    private $service;

    public function __construct()
    {
        $this->service = new \Controllers\Motion\Service();
    }

    /**
     *  Start / stop motion capture
     */
    public function startStop(string $status)
    {
        if ($status == 'start') {
            if (!file_exists(DATA_DIR . '/start-motion.request')) {
                touch(DATA_DIR . '/start-motion.request');
            }
        }
        if ($status == 'stop') {
            if (!file_exists(DATA_DIR . '/stop-motion.request')) {
                touch(DATA_DIR . '/stop-motion.request');
            }
        }
    }
}
