<?php

namespace Controllers\Service\Unit;

use Exception;

class Autostart extends \Controllers\Service\Service
{
    private $autostartController;
    private $motionServiceController;
    private $logController;

    public function __construct(string $unit)
    {
        parent::__construct($unit);

        $this->autostartController = new \Controllers\Motion\Autostart();
        $this->motionServiceController = new \Controllers\Motion\Service();
        $this->logController = new \Controllers\Log\Log();
    }

    /**
     *  Start motion service if a start request file is found
     */
    private function start() : void
    {
        if (!file_exists(DATA_DIR . '/start-motion.request')) {
            return;
        }

        parent::log('A start of motion service is required. Starting...');

        unlink(DATA_DIR . '/start-motion.request');

        try {
            $this->motionServiceController->start();
        } catch (Exception $e) {
            // Log to the web interface so the user can see the error
            $this->logController->log('error', 'Motion start request', 'Error while starting motion service: ' . $e->getMessage());
            parent::logError('Error while starting motion service: ' . $e->getMessage());
        }

        parent::log('Motion service successfully started');
    }

    /**
     *  Stop motion service if a stop request file is found
     */
    private function stop() : void
    {
        if (!file_exists(DATA_DIR . '/stop-motion.request')) {
            return;
        }

        parent::log('A stop of motion service is required. Stopping...');

        unlink(DATA_DIR . '/stop-motion.request');

        try {
            $this->motionServiceController->stop();
        } catch (Exception $e) {
            // Log to the web interface so the user can see the error
            $this->logController->log('error', 'Motion stop request', 'Error while stopping motion service: ' . $e->getMessage());
            parent::logError('Error while stopping motion service: ' . $e->getMessage());
        }

        parent::log('Motion service successfully stopped');
    }

    /**
     *  Restart motion service if a restart request file is found
     */
    private function restart() : void
    {
        if (!file_exists(DATA_DIR . '/restart-motion.request')) {
            return;
        }

        parent::log('A restart of motion service is required. Restarting...');

        unlink(DATA_DIR . '/restart-motion.request');

        /**
         *  Stop motion service
         */
        try {
            $this->motionServiceController->stop();
        } catch (Exception $e) {
            // Log to the web interface so the user can see the error
            $this->logController->log('error', 'Motion restart request', 'Error while stopping motion service: ' . $e->getMessage());
            parent::logError('Error while stopping motion service: ' . $e->getMessage());
            return;
        }

        parent::log('Motion service successfully stopped');

        /**
         *  Start motion service
         */
        try {
            $this->motionServiceController->start();
        } catch (Exception $e) {
            // Log to the web interface so the user can see the error
            $this->logController->log('error', 'Motion restart request', 'Error while starting motion service: ' . $e->getMessage());
            parent::logError('Error while starting motion service: ' . $e->getMessage());
            return;
        }

        parent::log('Motion service successfully restarted');
    }

    /**
     *  Run motion service autostart
     */
    public function run() : void
    {
        parent::log('Launching autostart');

        while (true) {
            // Set default value for autostart priority
            $priority = false;
            $priorityIs = 'devices';
            $startService = null;

            // Sleep 5 seconds to avoid high cpu usage
            sleep(5);

            // Check if a start of motion is required
            $this->start();

            // Check if a restart of motion is required
            $this->restart();

            // Check if a stop of motion is required
            $this->stop();

            /**
             *  Autostart mechanism
             *
             *  Always get settings to check if autostart and device presence are enabled, cause it
             *  can be disabled at any moment by the user from the web ui
             */
            if ($this->autostartController->getStatus() != 'enabled') {
                parent::logDebug('Time-slots and devices autostart is disabled');
                continue;
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
            $devicePresenceStatus = $this->autostartController->getDevicePresenceStatus();

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
            $timeSlots = $this->autostartController->getConfiguration();
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
                parent::logDebug('Current autostart priority is: ' . $priorityIs);
            }

            /**
             *  If there is no priority (meaning device presence is disabled) or if there is a priority and the priority is the time period
             *  then check if current time is between autostart time period
             */
            if (!$priority or ($priority and $priorityIs == 'time-period')) {
                // If motion service has to be started
                if ($startService === true) {
                    if (!$this->motionServiceController->isRunning()) {
                        parent::log('Starting motion according to autostart time configuration');

                        try {
                            $this->motionServiceController->start();
                        } catch (Exception $e) {
                            // Log to the web interface so the user can see the error
                            $this->logController->log('error', 'Motion autostart', 'Cannot start motion service: ' . $e->getMessage());
                            parent::logError('Cannot start motion service: ' . $e->getMessage());
                        }

                        parent::log('Motion service successfully started');
                    }
                }

                // If motion service has to be stopped
                if ($startService === false) {
                    if ($this->motionServiceController->isRunning()) {
                        parent::log('Stopping motion according to autostart time configuration');

                        try {
                            $this->motionServiceController->stop();
                        } catch (Exception $e) {
                            // Log to the web interface so the user can see the error
                            $this->logController->log('error', 'Motion autostart', 'Cannot stop motion service: ' . $e->getMessage());
                            parent::logError('Cannot stop motion service: ' . $e->getMessage());
                        }

                        parent::log('Motion service successfully stopped');
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
                    $devices = $this->autostartController->getDevices();

                    if (!empty($devices)) {
                        foreach ($devices as $device) {
                            parent::logDebug('Trying to reach device ' . $device['Name'] . ' (' . $device['Ip'] . ')');

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
                                    parent::logDebug('At least 1 active device has been found on the network - someone is home');
                                    if ($this->motionServiceController->isRunning()) {
                                        parent::log('Stopping motion according to autostart device presence configuration');

                                        try {
                                            $this->motionServiceController->stop();
                                        } catch (Exception $e) {
                                            // Log to the web interface so the user can see the error
                                            $this->logController->log('error', 'Motion autostart', 'Cannot stop motion service: ' . $e->getMessage());
                                            parent::logError('Cannot stop motion service: ' . $e->getMessage());
                                        }

                                        parent::log('Motion service successfully stopped');
                                    }

                                    // Break this 'while' loop and go back to the 'while true' loop
                                    continue 3;
                                }

                                $try++;
                            }
                        }

                        /**
                         *  If all the devices are absent from the local network, then start motion
                         *  Start motion only if not already running
                         */
                        parent::logDebug('No active device found on the network - nobody is home');

                        if (!$this->motionServiceController->isRunning()) {
                            parent::log('Starting motion according to autostart device presence configuration');

                            try {
                                $this->motionServiceController->start();
                            } catch (Exception $e) {
                                // Log to the web interface so the user can see the error
                                $this->logController->log('error', 'Motion autostart', 'Cannot start motion service: ' . $e->getMessage());
                                parent::logError('Cannot start motion service: ' . $e->getMessage());
                            }

                            parent::log('Motion service successfully started');
                        }
                    }
                } catch (Exception $e) {
                    // Log to the web interface so the user can see the error
                    $this->logController->log('error', 'Motion autostart', 'Error while executing autostart: ' . $e->getMessage());
                    parent::logError('Error while executing autostart: ' . $e->getMessage());
                }
            }
        }
    }
}
