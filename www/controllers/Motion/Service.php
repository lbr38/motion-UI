<?php

namespace Controllers\Motion;

use Exception;

class Service
{
    private $model;
    private $layoutContainerReloadController;

    public function __construct()
    {
        $this->model = new \Models\Motion\Service();
        $this->layoutContainerReloadController = new \Controllers\Layout\ContainerReload();
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
     *  Returns true if motion service is running
     */
    public function isRunning() : bool
    {
        $myprocess = new \Controllers\Process('/usr/sbin/service motion status');
        $myprocess->execute();
        $content = $myprocess->getOutput();
        $myprocess->close();

        if (preg_match('/.*process is running with PID [1-9]\d*/', $content)) {
            return true;
        }

        return false;
    }

    /**
     *  Stop motion service
     */
    public function stop() : bool
    {
        $myprocess = new \Controllers\Process('/usr/sbin/service motion stop');
        $myprocess->execute();
        $myprocess->getOutput();
        $myprocess->close();

        if ($myprocess->getExitCode() != 0) {
            return false;
        }

        // Set motion status to inactive in database
        $this->setStatusInDb('inactive');
        $this->layoutContainerReloadController->reload('motion/buttons/main');

        return true;
    }

    /**
     *  Start motion service
     */
    public function start() : bool
    {
        $myprocess = new \Controllers\Process('/usr/sbin/service motion start');
        $myprocess->execute();
        $myprocess->getOutput();
        $myprocess->close();

        if ($myprocess->getExitCode() != 0) {
            return false;
        }

        // Set motion status to active in database
        $this->setStatusInDb('active');
        $this->layoutContainerReloadController->reload('motion/buttons/main');

        return true;
    }

    /**
     *  Restart motion service if it is running
     */
    public function restart() : void
    {
        if (!$this->isRunning()) {
            return;
        }

        if (!file_exists(DATA_DIR . '/motion.restart')) {
            touch(DATA_DIR . '/motion.restart');
        }
    }

    /**
     *  Returns true if motionui service is running
     */
    public function motionuiServiceRunning() : bool
    {
        $myprocess = new \Controllers\Process('ps aux | grep "motionui.service" | grep -v grep');
        $myprocess->execute();
        $myprocess->close();

        if ($myprocess->getExitCode() != 0) {
            return false;
        }

        return true;
    }

    /**
     *  Return motion service log content
     */
    public function getLog() : string
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to view motion service logs');
        }

        if (!file_exists('/var/log/motion/motion.log')) {
            throw new Exception('No log for motion service yet');
        }

        $content = file_get_contents('/var/log/motion/motion.log');

        if ($content === false) {
            throw new Exception('Failed to read log file');
        }

        return $content;
    }
}
