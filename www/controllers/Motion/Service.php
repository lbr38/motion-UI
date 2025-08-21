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
        $myprocess->getOutput();
        $myprocess->close();

        if ($myprocess->getExitCode() == 0) {
            return true;
        }

        return false;
    }

    /**
     *  Stop motion service
     */
    public function stop() : void
    {
        $myprocess = new \Controllers\Process('/usr/sbin/service motion stop');
        $myprocess->execute();
        $output = trim($myprocess->getOutput());
        $myprocess->close();

        if ($myprocess->getExitCode() != 0) {
            throw new Exception('Error while stopping motion service: ' . $output);
        }

        // Set motion status to inactive in database
        $this->setStatusInDb('inactive');
        $this->layoutContainerReloadController->reload('motion/buttons/main');
        $this->layoutContainerReloadController->reload('buttons/top');
    }

    /**
     *  Start motion service
     */
    public function start() : void
    {
        $myprocess = new \Controllers\Process('/usr/sbin/service motion start');
        $myprocess->execute();
        $output = trim($myprocess->getOutput());
        $myprocess->close();

        if ($myprocess->getExitCode() != 0) {
            throw new Exception('Error while starting motion service: ' . $output);
        }

        // Set motion status to active in database
        $this->setStatusInDb('active');
        $this->layoutContainerReloadController->reload('motion/buttons/main');
        $this->layoutContainerReloadController->reload('buttons/top');
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
    public function getLog(string $log) : string
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to view motion service logs');
        }

        $log = realpath('/var/log/motion/' . $log);

        if (!preg_match('#^/var/log/motion/.*log#', $log)) {
            throw new Exception('Invalid log file');
        }

        if (!file_exists($log)) {
            throw new Exception('Log file does not exist');
        }

        $content = file_get_contents($log);

        if ($content === false) {
            throw new Exception('Failed to read log file');
        }

        return $content;
    }
}
