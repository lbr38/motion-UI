<?php

namespace Controllers\Motion;

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
    public function enable(string $status)
    {
        if ($status != 'enabled' and $status != 'disabled') {
            throw new Exception('Invalid parameter');
        }

        $this->model->enable($status);
    }

    /**
     *  Enable / disable autostart on device presence
     */
    public function enableDevicePresence(string $status)
    {
        if ($status != 'enabled' and $status != 'disabled') {
            throw new Exception('Invalid parameter');
        }

        $this->model->enableDevicePresence($status);
    }

    /**
     *  Configure motion autostart
     */
    public function configure(string $mondayStart, string $mondayEnd, string $tuesdayStart, string $tuesdayEnd, string $wednesdayStart, string $wednesdayEnd, string $thursdayStart, string $thursdayEnd, string $fridayStart, string $fridayEnd, string $saturdayStart, string $saturdayEnd, string $sundayStart, string $sundayEnd)
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
            \Controllers\Common::validateData($sundayEnd)
        );
    }

    /**
     *  Execute autostart
     */
    public function autostart()
    {
        $this->log('Running autostart');

        while (true) {
            pcntl_signal_dispatch();
            sleep(5);

            // Set default value for priority
            $priority = false;
            $priorityIs = 'devices';
            $startService = null;

            /**
             *  Stop autostart if stop file exists
             */
            if (file_exists(DATA_DIR . '/.service-autostart-stop')) {
                unlink(DATA_DIR . '/.service-autostart-stop');
                exit;
            }

            /**
             *  Always get settings to check if autostart and device presence are enabled, cause it
             *  can be disabled at any moment by the user from the web ui
             */
            if ($this->getStatus() != 'enabled') {
                $this->log('Autostart is disabled');
                exit;
            }

            /**
             *  Get actual day and time
             */
            $day = date('l');
            $time = time();
            $today = date('Y-m-d');
            $previousDay = date('l', strtotime('yesterday'));
            $yesterday = date('Y-m-d', strtotime('yesterday'));

            /**
             *  Get device presence status
             */
            $devicePresenceStatus = $this->getDevicePresenceStatus();

            /**
             *  If device presence is enabled, a priority have to be set between time period and device presence
             *  Set it to 'devices' then if time period is set, a check will have to be done to see if the time period is prioritary
             */
            if ($devicePresenceStatus == 'enabled') {
                $priority = true;
                $priorityIs = 'devices';
            }

            /**
             *  Get autostart time period configuration for the actual day and for yesterday
             */
            $timeSlots = $this->getConfiguration();
            $autostartTodayStart = $timeSlots[$day . '_start'];
            $autostartTodayEnd = $timeSlots[$day . '_end'];
            $autostartYesterdayStart = $timeSlots[$previousDay . '_start'];
            $autostartYesterdayEnd = $timeSlots[$previousDay . '_end'];

            /**
             *  If today's time period end is 00:00, then set it to 23:59:59 to be able to compare it with the current time
             */
            if ($autostartTodayEnd == '00:00') {
                $autostartTodayEnd = '23:59:59';
            }

            /**
             *  If yesterday's time period is set (meaning it's not --:--)
             */
            if (!empty($autostartYesterdayStart) and !empty($autostartYesterdayEnd)) {
                $autostartYesterdayStartTime = strtotime("$yesterday $autostartYesterdayStart");
                $autostartYesterdayEndTime   = strtotime("$yesterday $autostartYesterdayEnd");

                // If yesterday's ending time period ends after 00:00, meaning it spills over into today
                if ($autostartYesterdayStart > $autostartYesterdayEnd) {
                    $autostartYesterdayEndTime   = strtotime("$today $autostartYesterdayEnd");

                    // Check if current time falls within yesterday's time period
                    if ($time >= $autostartYesterdayStartTime && $time < $autostartYesterdayEndTime) {
                        $priorityIs = 'time-period';
                        $startService = true;
                    } else {
                        $startService = false;
                    }
                }
            }

            /**
             *  If today's time period is set (meaning it's not --:--)
             */
            if (!empty($autostartTodayStart) and !empty($autostartTodayEnd)) {
                $autostartTodayStartTime = strtotime("$today $autostartTodayStart");
                $autostartTodayEndTime   = strtotime("$today $autostartTodayEnd");

                /**
                 *  Handle case where end time is past midnight
                 */
                if ($autostartTodayStartTime > $autostartTodayEndTime) {
                    $autostartTodayEndTime = strtotime("$today $autostartTodayEnd +1 day");
                }

                /**
                 *  If autostart slots are set to 00:00, then the service must run all the time
                 */
                if ($autostartTodayStart == '00:00' and $autostartTodayEnd == '00:00') {
                    $priorityIs = 'time-period';
                    $startService = true;

                /**
                 *  Otherwise, some checks are needed to check if the service must be started or stopped
                 */
                } else {
                    if (!empty($autostartYesterdayStart) and !empty($autostartYesterdayEnd)) {
                        if (($time >= $autostartTodayStartTime && $time < $autostartTodayEndTime) || ($time >= $autostartYesterdayStartTime && $time < $autostartYesterdayEndTime)) {
                            $priorityIs = 'time-period';
                            $startService = true;
                        } else {
                            $startService = false;
                        }
                    } else {
                        // Check if current time falls within today's or yesterday's time period
                        if ($time >= $autostartTodayStartTime && $time < $autostartTodayEndTime) {
                            $priorityIs = 'time-period';
                            $startService = true;
                        } else {
                            $startService = false;
                        }
                    }
                }
            }

            /**
             *  Print priority, if there is one
             */
            if ($priority) {
                $this->log('Current autostart priority is: ' . $priorityIs);
            }

            /**
             *  If there is no priority (meaning device presence is disabled) or if there is a priority and the priority is the time period
             *  then check if current time is between autostart time period
             */
            if (!$priority or ($priority and $priorityIs == 'time-period')) {
                // If motion service has to be started
                if ($startService === true) {
                    if (!$this->motionService->isRunning()) {
                        $this->log('Starting motion according to autostart time configuration');
                        if (!$this->motionService->start()) {
                            $this->logController->log('error', 'Motion autostart', 'Cannot start motion service');
                        }
                    }
                }

                // If motion service has to be stopped
                if ($startService === false) {
                    if ($this->motionService->isRunning()) {
                        $this->log('Stopping motion according to autostart time configuration');
                        if (!$this->motionService->stop()) {
                            $this->logController->log('error', 'Motion autostart', 'Cannot stop motion service');
                        }
                    }
                }

                // If time period is the priority, then skip device presence check (skip next step)
                continue;
            }

            /**
             *  If device presence is enabled, check that at least one device is present
             */
            if ($devicePresenceStatus == 'enabled') {
                try {
                    // Get known devices
                    $devices = $this->getDevices();

                    if (!empty($devices)) {
                        foreach ($devices as $device) {
                            $this->log('Trying to ping device ' . $device['Name'] . ' (' . $device['Ip'] . ')');

                            /**
                             *  Try to ping the first device of the loop
                             *  If there is a response, then stop motion cause there is at least 1 device present on the network.
                             *  Else, try to ping the next device
                             */

                            // There will be 2 pings tries for the same device. Some devices sometimes not respond on the first try but do on the second.
                            $try = 0;

                            while ($try != 2) {
                                $myprocess = new \Controllers\Process('ping -q -c1 -W2 -n "' . $device['Ip'] . '" > /dev/null');
                                $myprocess->execute();
                                $myprocess->close();

                                /**
                                 *  If ping is successful, stop motion
                                 */
                                if ($myprocess->getExitCode() == 0) {
                                    $this->log('At least 1 active device has been found on the network - someone is home');
                                    if ($this->motionService->isRunning()) {
                                        $this->log('Stopping motion according to autostart device presence configuration');
                                        if (!$this->motionService->stop()) {
                                            $this->logController->log('error', 'Motion autostart', 'Cannot stop motion service');
                                        }
                                    }

                                    // Break this 'while' loop and go back to the 'while true' loop
                                    sleep(5);
                                    continue 3;
                                }

                                $try++;
                            }
                        }

                        /**
                         *  If all the devices are absent from the local network, then start motion
                         *  Start motion only if not already running
                         */
                        $this->log('No active device found on the network - nobody is home');
                        if (!$this->motionService->isRunning()) {
                            $this->log('Starting motion according to autostart device presence configuration');
                            if (!$this->motionService->start()) {
                                $this->logController->log('error', 'Motion autostart', 'Cannot start motion service');
                            }
                        }
                    }
                } catch (Exception $e) {
                    $this->logController->log('error', 'Motion autostart', 'Error while executing autostart: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     *  Return autostart log
     */
    public function getLog() : string
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to view motion autostart logs');
        }

        if (!file_exists(AUTOSTART_LOGS_DIR . '/' . date('Y-m-d') . '_autostart.log')) {
            throw new Exception('No log for motion autostart yet');
        }

        $content = file_get_contents(AUTOSTART_LOGS_DIR . '/' . date('Y-m-d') . '_autostart.log');

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

    /**
     *  Clean autostart logs
     */
    public function clean() : void
    {
        if (!is_dir(AUTOSTART_LOGS_DIR)) {
            return;
        }

        /**
         *  Get all log files
         */
        $logFiles = glob(AUTOSTART_LOGS_DIR . '/*.log');

        if (empty($logFiles)) {
            return;
        }

        /**
         *  Remove logs older than 7 days
         */
        foreach ($logFiles as $logFile) {
            if (filemtime($logFile) < strtotime('-7 days')) {
                unlink($logFile);
            }
        }
    }
}
