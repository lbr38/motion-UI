<?php

namespace Controllers\Service;

use Exception;
use Datetime;
use Controllers\Log\Cli as CliLog;

class Monitoring extends Service
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new \Models\Service\Monitoring();
    }

    /**
     *  Start the monitoring service
     */
    public function execute() : void
    {
        CliLog::log('Starting monitoring service...');

        while (true) {
            try {
                // Get current CPU usage
                $cpuUsage = \Controllers\System\Cpu::getUsage();

                // Get current memory usage
                $memoryUsage = \Controllers\System\Memory::getUsage();

                if (!is_numeric($cpuUsage)) {
                    CliLog::error('Monitoring service', 'Failed to retrieve CPU usage.');
                    sleep(30);
                    continue;
                }

                if (!is_numeric($memoryUsage)) {
                    CliLog::error('Monitoring service', 'Failed to retrieve memory usage.');
                    sleep(30);
                    continue;
                }

                // Save results to the database
                $this->model->save(time(), $cpuUsage, $memoryUsage);

                // Clean up old data (older than 3 days)
                $this->model->clean(time() - 259200); // 3 days in seconds
            } catch (Exception $e) {
                CliLog::error('Monitoring service', 'Exception: ' . $e->getMessage());
            } catch (Error $e) {
                CliLog::error('Monitoring service', 'Error: ' . $e->getMessage());
            }

            sleep(60);
        }
    }
}
