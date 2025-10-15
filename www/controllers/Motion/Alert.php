<?php

namespace Controllers\Motion;

use Controllers\Utils\Validate;
use Exception;

class Alert
{
    private $model;

    public function __construct()
    {
        $this->model = new \Models\Motion\Alert();
    }

    /**
     *  Returns actual alerts time slots configuration
     */
    public function getConfiguration()
    {
        /**
         *  Get actual configuration
         */
        $configuration = $this->model->getConfiguration();

        return $configuration;
    }

    /**
     *  Returns alert parameter status
     */
    public function getStatus()
    {
        $status = $this->getConfiguration();

        return $status['Status'];
    }

    /**
     *  Enable / disable motion alerts
     */
    public function enable(string $status)
    {
        if ($status != 'enabled' and $status != 'disabled') {
            throw new Exception('Invalid parameter');
        }

        $this->model->enable($status);
    }

    /**
     *  Configure motion alerts
     */
    public function configure(string $mondayStart, string $mondayEnd, string $tuesdayStart, string $tuesdayEnd, string $wednesdayStart, string $wednesdayEnd, string $thursdayStart, string $thursdayEnd, string $fridayStart, string $fridayEnd, string $saturdayStart, string $saturdayEnd, string $sundayStart, string $sundayEnd, string $mailRecipient)
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
            Validate::string($sundayEnd),
            Validate::string($mailRecipient)
        );
    }
}
