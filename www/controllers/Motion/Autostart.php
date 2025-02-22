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
        /**
         *  Get actual configuration
         */
        $configuration = $this->model->getConfiguration();

        return $configuration;
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
        while (true) {
            pcntl_signal_dispatch();

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
                echo 'Autostart is disabled' . PHP_EOL;
                exit;
            }

            /**
             *  Get device presence status
             */
            $devicePresenceStatus = $this->getDevicePresenceStatus();

            echo 'Running autostart' . PHP_EOL;

            /**
             *  If device presence is enabled, check that at least one device is present
             */
            if ($devicePresenceStatus == 'enabled') {
                try {
                    /**
                     *  Get known devices
                     */
                    $devices = $this->getDevices();

                    if (!empty($devices)) {
                        foreach ($devices as $device) {
                            echo 'Trying to ping device ' . $device['Name'] . ' (' . $device['Ip'] . ')' . PHP_EOL;

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
                                    if ($this->motionService->isRunning()) {
                                        echo 'At least 1 active device has been found on the network - someone is home - stopping motion' . PHP_EOL;
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
                        if (!$this->motionService->isRunning()) {
                            echo 'No active device found on the network - nobody is home - starting motion' . PHP_EOL;
                            if (!$this->motionService->start()) {
                                $this->logController->log('error', 'Motion autostart', 'Cannot start motion service');
                            }
                        }
                    }
                } catch (Exception $e) {
                    $this->logController->log('error', 'Motion autostart', 'Errow while executing autostart: ' . $e->getMessage());
                }
            }

            if ($devicePresenceStatus != 'enabled') {
                /**
                 *  If device presence is not enabled, start/stop motion on configured time slots
                 */

                /**
                 *  Get actual day and time
                 */
                $day = date('l');

                /**
                 *  Get autostart time slots configuration for actual day
                 */
                $timeSlots = $this->getConfiguration();
                $autostartTodayStart = $timeSlots[$day . '_start'];
                $autostartTodayEnd = $timeSlots[$day . '_end'];

                /**
                 *  If autostart time slot end is 00:00, then set it to 23:59:59 to be able to compare it with actual time
                 */
                if ($autostartTodayEnd == '00:00') {
                    $autostartTodayEnd = '23:59:59';
                }

                /**
                 *  If no autostart is configured for the actual day, then stop motion and quit
                 *  (no autostart configured means motion should not be started)
                 */
                if (empty($autostartTodayStart) || empty($autostartTodayEnd)) {
                    echo 'No autostart configured for today' . PHP_EOL;

                    if ($this->motionService->isRunning()) {
                        echo 'Stopping motion' . PHP_EOL;
                        if (!$this->motionService->stop()) {
                            $this->logController->log('error', 'Motion autostart', 'Cannot stop motion service');
                        }
                    }

                    sleep(5);
                    continue;
                }

                /**
                 *  If actual time is between autostart time slot, then start motion
                 */
                $time = time();

                // Today and yesterday's dates
                $today = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime('yesterday'));

                // Today's autostart times
                $autostartTodayStart = $timeSlots[date('l') . '_start'];
                $autostartTodayEnd = $timeSlots[date('l') . '_end'];

                // Yesterday's autostart times
                $previousDay = date('l', strtotime('yesterday'));
                $autostartYesterdayStart = $timeSlots[$previousDay . '_start'];
                $autostartYesterdayEnd = $timeSlots[$previousDay . '_end'];

                // Adjust yesterday's end time if it's '00:00'
                if ($autostartYesterdayEnd == '00:00') {
                    $autostartYesterdayEnd = '23:59:59';
                }

                // Calculate timestamps
                $autostartTodayStartTime = strtotime("$today $autostartTodayStart");
                $autostartTodayEndTime = strtotime("$today $autostartTodayEnd");

                // Handle case where end time is past midnight
                if ($autostartTodayStartTime > $autostartTodayEndTime) {
                    $autostartTodayEndTime = strtotime("$today $autostartTodayEnd +1 day");
                }

                $autostartYesterdayStartTime = strtotime("$yesterday $autostartYesterdayStart");
                $autostartYesterdayEndTime = strtotime("$yesterday $autostartYesterdayEnd");

                // Handle case where end time is past midnight
                if ($autostartYesterdayStartTime > $autostartYesterdayEndTime) {
                    $autostartYesterdayEndTime = strtotime("$yesterday $autostartYesterdayEnd +1 day");
                }

                // Check if current time falls within today's or yesterday's time slots
                if (($time >= $autostartTodayStartTime && $time < $autostartTodayEndTime) ||
                ($time >= $autostartYesterdayStartTime && $time < $autostartYesterdayEndTime)) {
                    /**
                     *  Start motion only if not already running
                     */
                    if (!$this->motionService->isRunning()) {
                        echo 'Starting motion according to autostart time configuration' . PHP_EOL;
                        if (!$this->motionService->start()) {
                            $this->logController->log('error', 'Motion autostart', 'Cannot start motion service');
                        }
                    }
                } else {
                    /**
                     *  Else stop motion if running
                     */
                    if ($this->motionService->isRunning()) {
                        echo 'Stopping motion according to autostart time configuration' . PHP_EOL;
                        if (!$this->motionService->stop()) {
                            $this->logController->log('error', 'Motion autostart', 'Cannot stop motion service');
                        }
                    }
                }
            }

            sleep(5);
        }
    }
}
