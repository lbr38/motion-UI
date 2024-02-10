<?php

namespace Controllers\Motion;

use Exception;

class Service
{
    private $model;
    private $layoutContainerStateController;

    public function __construct()
    {
        $this->model = new \Models\Motion\Service();
        $this->layoutContainerStateController = new \Controllers\Layout\ContainerState();
    }

    /**
     *  Get daily motion service status (for stats)
     */
    public function getMotionServiceStatusStats()
    {
        return $this->model->getMotionServiceStatusStats();
    }

    /**
     *  Set motion actual status in database
     */
    public function setStatusInDb(string $status)
    {
        $this->model->setStatusInDb($status);
    }

    /**
     *  Return motion service status
     */
    public function isRunning()
    {
        $myprocess = new \Controllers\Process('/usr/sbin/service motion status');
        $myprocess->execute();
        $content = $myprocess->getOutput();
        $myprocess->close();

        if (preg_match('/.*Running process for motion : [1-9]\d*/', $content)) {
            return true;
        }

        return false;
    }

    /**
     *  Stop motion service
     */
    public function stop()
    {
        $myprocess = new \Controllers\Process('/usr/sbin/service motion stop');
        $myprocess->execute();
        $myprocess->getOutput();
        $myprocess->close();

        if ($myprocess->getExitCode() != 0) {
            return false;
        }

        $this->layoutContainerStateController->update('motion/buttons/main');

        return true;
    }

    /**
     *  Start motion service
     */
    public function start()
    {
        $myprocess = new \Controllers\Process('/usr/sbin/service motion start');
        $myprocess->execute();
        $myprocess->getOutput();
        $myprocess->close();

        if ($myprocess->getExitCode() != 0) {
            return false;
        }

        $this->layoutContainerStateController->update('motion/buttons/main');

        return true;
    }

    /**
     *  Returns status of systemd 'motionui' service
     */
    public function motionuiServiceRunning()
    {
        $myprocess = new \Controllers\Process('ps aux | grep "motionui.service" | grep -v grep');
        $myprocess->execute();
        $myprocess->close();

        if ($myprocess->getExitCode() != 0) {
            return false;
        }

        return true;
    }
}
