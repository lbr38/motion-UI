<?php

namespace Controllers\Motion;

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
            \Controllers\Common::validateData($mondayStart),
            \Controllers\Common::validateData($mondayEnd),
            \Controllers\Common::validateData($tuesdayStart),
            \Controllers\Common::validateData($tuesdayEnd),
            \Controllers\Common::validateData($wednesdayStart),
            \Controllers\Common::validateData($wednesdayEnd),
            \Controllers\Common::validateData($thursdayStart),
            \Controllers\Common::validateData($thursdayEnd),
            \Controllers\Common::validateData($fridayStart),
            \Controllers\Common::validateData($fridayEnd),
            \Controllers\Common::validateData($saturdayStart),
            \Controllers\Common::validateData($saturdayEnd),
            \Controllers\Common::validateData($sundayStart),
            \Controllers\Common::validateData($sundayEnd),
            \Controllers\Common::validateData($mailRecipient)
        );
    }
}
