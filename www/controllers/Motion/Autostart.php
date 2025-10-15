<?php

namespace Controllers\Motion;

use Controllers\Utils\Validate;
use Exception;

class Autostart
{
    private $model;
    private $motionService;
    private $logController;

    public function __construct()
    {
        $this->model = new \Models\Motion\Autostart();
        $this->motionService = new \Controllers\Motion\Service();
        $this->logController = new \Controllers\Log\Log();
    }

    /**
     *  Returns actual autostart time slots configuration
     */
    public function getConfiguration()
    {
        return $this->model->getConfiguration();
    }

    /**
     *  Returns autostart parameter status
     */
    public function getStatus()
    {
        $status = $this->getConfiguration();

        return $status['Status'];
    }

    /**
     *  Returns autostart on device presence parameter status
     */
    public function getDevicePresenceStatus()
    {
        $status = $this->getConfiguration();

        return $status['Device_presence'];
    }

    /**
     *  Returns known devices
     */
    public function getDevices()
    {
        return $this->model->getDevices();
    }

    /**
     *  Enable / disable motion autostart
     */
    public function enable(string $status) : void
    {
        if ($status != 'enabled' and $status != 'disabled') {
            throw new Exception('Invalid parameter');
        }

        $this->model->enable($status);
    }

    /**
     *  Enable / disable autostart on device presence
     */
    public function enableDevicePresence(string $status) : void
    {
        if ($status != 'enabled' and $status != 'disabled') {
            throw new Exception('Invalid parameter');
        }

        $this->model->enableDevicePresence($status);
    }

    /**
     *  Configure motion autostart
     */
    public function configure(string $mondayStart, string $mondayEnd, string $tuesdayStart, string $tuesdayEnd, string $wednesdayStart, string $wednesdayEnd, string $thursdayStart, string $thursdayEnd, string $fridayStart, string $fridayEnd, string $saturdayStart, string $saturdayEnd, string $sundayStart, string $sundayEnd) : void
    {
        $this->model->configure(
            Validate::string($mondayStart),
            Validate::string($mondayEnd),
            Validate::string($tuesdayStart),
            Validate::string($tuesdayEnd),
            Validate::string($wednesdayStart),
            Validate::string($wednesdayEnd),
            Validate::string($thursdayStart),
            Validate::string($thursdayEnd),
            Validate::string($fridayStart),
            Validate::string($fridayEnd),
            Validate::string($saturdayStart),
            Validate::string($saturdayEnd),
            Validate::string($sundayStart),
            Validate::string($sundayEnd)
        );
    }

    /**
     *  Return autostart log
     */
    public function getLog(string $log) : string
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to view motion autostart logs');
        }

        $log = realpath(AUTOSTART_LOGS_DIR . '/' . $log);

        if (!preg_match('#^' . AUTOSTART_LOGS_DIR . '/.*log#', $log)) {
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

    /**
     *  Log to console and to autostart log file
     */
    private function log(string $message) : void
    {
        $log = '[' . date('D M j H:i:s') . ']' . ' ' . $message;

        // Log to autostart log file
        file_put_contents(AUTOSTART_LOGS_DIR . '/' . date('Y-m-d') . '_autostart.log', $log . PHP_EOL, FILE_APPEND);

        // Log to console
        echo $log . PHP_EOL;
    }
}
