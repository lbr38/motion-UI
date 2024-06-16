<?php

namespace Controllers\Service;

use Exception;
use Datetime;

class Service
{
    private $logController;
    private $motionController;
    private $motionEventController;
    private $motionAutostartController;
    private $motionServiceController;
    private $timelapseController;

    private $curlHandle;
    private $autostart;
    private $autostartDevicePresenceEnabled;
    private $timelapse;
    private $timelapseRetention;
    private $devicesIp;
    private $alertEnabled;
    private $alertRecipient;
    private $eventRetention = 30;
    private $capturesDir = '/var/lib/motion';

    public function __construct()
    {
        $this->motionEventController = new \Controllers\Motion\Event();
        $this->motionController = new \Controllers\Motion\Motion();
        $this->motionAutostartController = new \Controllers\Motion\Autostart();
        $this->motionServiceController = new \Controllers\Motion\Service();
        $this->timelapseController = new \Controllers\Camera\Timelapse();
    }

    /**
     *  Get some global settings for the service to run
     */
    private function getSettings()
    {
        echo $this->getDate() . ' Getting settings...' . PHP_EOL;

        $mysettings = new \Controllers\Settings();
        $mytimelapse = new \Controllers\Camera\Timelapse();
        $mymotionAlert = new \Controllers\Motion\Alert();
        $mymotionAutostart = new \Controllers\Motion\Autostart();

        /**
         *  Loop until all settings are retrieved
         */
        while (true) {
            $missingSetting = 0;

            /**
             *  Get all settings
             */
            $settings = $mysettings->get();

            /**
             *  Timelapse enable status
             */
            $this->timelapse = $mytimelapse->enabled();

            /**
             *  Timelapse retention
             */
            $this->timelapseRetention = $settings['Timelapse_retention'];

            /**
             *  Motion events retention
             */
            $this->eventRetention = $settings['Motion_events_retention'];

            /**
             *  Autostart settings
             */
            $this->autostart = $mymotionAutostart->getStatus();
            $this->autostartDevicePresenceEnabled = $mymotionAutostart->getDevicePresenceStatus();

            if ($this->autostart == 'enabled') {
                $autostartConfiguration = $mymotionAutostart->getConfiguration();

                /**
                 *  Get devices IP
                 */
                $devices = $mymotionAutostart->getDevices();
                $this->devicesIp = array_column($devices, 'ip');
            }

            /**
             *  Alert settings
             */
            $this->alertEnabled = $mymotionAlert->getStatus();

            if ($this->alertEnabled === true) {
                $alertConfiguration = $mymotionAlert->getConfiguration();

                /**
                 *  Get recipient
                 */
                $this->alertRecipient = $alertConfiguration['Recipient'];

                if (empty($this->alertRecipient)) {
                    $this->logController->log('error', 'Service', 'No mail recipient configured, alert(s) will not be send.');
                    $missingSetting++;
                }
            }

            /**
             *  Quit loop if all settings are retrieved
             */
            if ($missingSetting == 0) {
                break;
            }

            pcntl_signal_dispatch();
            sleep(5);
        }
    }

    /**
     *  Get notifications
     */
    private function getNotifications()
    {
        echo $this->getDate() . ' Getting notifications...' . PHP_EOL;

        try {
            $mynotification = new \Controllers\Notification();
            $mynotification->retrieve();
        } catch (Exception $e) {
            $this->logController->log('error', 'Service', 'Error while retrieving notifications: ' . $e->getMessage());
        }
    }

    /**
     *  Get current date and time
     */
    private function getDate()
    {
        return '[' . date('D M j H:i:s') . ']';
    }

    /**
     *  Check if a new version is available on Github
     */
    private function checkVersion()
    {
        echo $this->getDate() . ' Checking for a new version on github...' . PHP_EOL;

        try {
            $outputFile = fopen(DATA_DIR . '/version.available', "w");

            curl_setopt($this->curlHandle, CURLOPT_URL, 'https://raw.githubusercontent.com/lbr38/motion-UI/main/www/version');
            curl_setopt($this->curlHandle, CURLOPT_FILE, $outputFile);
            curl_setopt($this->curlHandle, CURLOPT_TIMEOUT, 30);

            /**
             *  Execute curl
             */
            curl_exec($this->curlHandle);

            /**
             *  If curl has failed (meaning a curl param might be invalid)
             */
            if (curl_errno($this->curlHandle)) {
                curl_close($this->curlHandle);
                fclose($outputFile);

                throw new Exception('Error while retrieving new version from Github (curl error): ' . curl_error($this->curlHandle));
            }

            /**
             *  Check that the http return code is 200 (the file has been downloaded)
             */
            $status = curl_getinfo($this->curlHandle);

            if ($status["http_code"] != 200) {
                /**
                 *  If return code is 404
                 */
                if ($status["http_code"] == '404') {
                    throw new Exception('Error while retrieving new version from Github (file not found)');
                } else {
                    throw new Exception('Error while retrieving new version from Github (http return code is: ' . $status["http_code"] . ')');
                }

                curl_close($this->curlHandle);
                fclose($outputFile);
            }
        } catch (Exception $e) {
            $this->logController->log('error', 'Service', $e->getMessage());
        }
    }

    /**
     *  Check if a service restart is needed
     */
    private function checkRestartNeeded(string $service)
    {
        if (file_exists(DATA_DIR . '/' . $service . '.restart')) {
            echo $this->getDate() . ' A restart of ' . $service . ' service is required. Restarting...' . PHP_EOL;
            unlink(DATA_DIR . '/' . $service . '.restart');

            $myprocess = new \Controllers\Process('/usr/sbin/service ' . $service . ' restart');
            $myprocess->execute();
            $myprocess->close();

            if ($myprocess->getExitCode() != 0) {
                echo $this->getDate() . ' Error restarting ' . $service . ' service';
            }
        }
    }

    /**
     *  Check if a start or stop of motion is needed
     */
    private function startStopMotion()
    {
        /**
         *  Start motion if following file is present
         */
        if (file_exists(DATA_DIR . '/start-motion.request')) {
            echo $this->getDate() . ' A start of motion service is required. Starting...' . PHP_EOL;
            unlink(DATA_DIR . '/start-motion.request');

            if ($this->motionServiceController->start()) {
                /**
                 *  Add service status in database
                 */
                $this->motionServiceController->setStatusInDb('active');
            } else {
                $this->logController->log('error', 'Service', 'Error while starting motion service.');
            }
        }

        /**
         *  Stop motion if following file is present
         */
        if (file_exists(DATA_DIR . '/stop-motion.request')) {
            echo $this->getDate() . ' A stop of motion service is required. Stopping...' . PHP_EOL;
            unlink(DATA_DIR . '/stop-motion.request');

            if ($this->motionServiceController->stop()) {
                /**
                 *  Add service status in database
                 */
                $this->motionServiceController->setStatusInDb('inactive');
            } else {
                $this->logController->log('error', 'Service', 'Error while stopping motion service.');
            }
        }
    }

    /**
     *  Check current motion service status and add it into database
     */
    private function monitorMotionStatus()
    {
        $status = 'inactive';

        if ($this->motionServiceController->isRunning() === true) {
            $status = 'active';
        }

        $this->motionServiceController->setStatusInDb($status);
    }

    /**
     *  Clean timelapse images and motion events depending on retention
     */
    private function cleanTimelapseAndMotionEvents()
    {
        /**
         *  Clean events every day at midnight only
         */
        if (date('H:i') != '00:00') {
            return;
        }

        echo $this->getDate() . ' Cleaning timelapse images and motion events...' . PHP_EOL;

        /**
         *  Clean timelapse images
         */
        $this->timelapseController->clean($this->timelapseRetention);

        /**
         *  Clean events
         */
        $this->motionEventController->clean($this->eventRetention);
    }

    /**
     *  Main function
     */
    public function run()
    {
        $this->logController = new \Controllers\Log\Log();
        $this->curlHandle = curl_init();

        $counter = 0;

        while (true) {
            /**
             *  Check if a motion or motionui service restart is needed
             */
            $this->checkRestartNeeded('motion');
            $this->checkRestartNeeded('motionui');

            /**
             *  Check if a start/stop of motion service is needed
             */
            $this->startStopMotion();

            /**
             *  Get settings
             */
            $this->getSettings();

            /**
             *  Execute autostart
             */
            if ($this->autostart == 'enabled') {
                $this->runService('autostart');
            }

            /**
             *  Execute timelapse
             */
            if ($this->timelapse === true) {
                $this->runService('timelapse');
            }

            /**
             *  Clean timelapse and events depending on retention
             */
            $this->cleanTimelapseAndMotionEvents();

            /**
             *  Execute actions on service start (counter = 0) and then every hour (counter = 720)
             *  3600 / 5sec (sleep 5) = 720
             */
            if ($counter == 0 || $counter == 720) {
                /**
                 *  Check version
                 */
                $this->checkVersion();

                /**
                 *  Get notifications
                 */
                $this->getNotifications();

                /**
                 *  Every hour, check motion service and add its status in database
                 */
                $this->monitorMotionStatus();

                /**
                 *  Reset counter
                 */
                $counter = 0;
            }

            pcntl_signal_dispatch();
            sleep(5);

            $counter++;
        }
    }

    /**
     *  Run this service with the specified parameter
     */
    private function runService(string $parameter)
    {
        try {
            /**
             *  Check if the service with specified parameter is already running (a php process must be running)
             */
            $myprocess = new \Controllers\Process("/usr/bin/ps aux | grep 'motionui." . $parameter . "' | grep -v grep");
            $myprocess->execute();
            $content = $myprocess->getOutput();
            $myprocess->close();

            /**
             *  Quit if there is already a process running
             */
            if ($myprocess->getExitCode() == 0) {
                return;
            }

            /**
             *  Else, run the service with the specified parameter
             */
            echo $this->getDate() . " Running service with parameter '" . $parameter . "'..." . PHP_EOL;

            $myprocess = new \Controllers\Process("/usr/bin/php " . ROOT . "/tools/service.php '" . $parameter . "' >/dev/null 2>/dev/null &");
            $myprocess->execute();
            $myprocess->close();
        } catch (Exception $e) {
            $this->logController->log('error', 'Service', 'Error while launching service with parameter '. $parameter . ': ' . $e->getMessage());
        }
    }
}
