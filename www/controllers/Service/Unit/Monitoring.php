<?php

namespace Controllers\Service\Unit;

use Controllers\System\Monitoring\Cpu as Cpu;
use Controllers\System\Monitoring\Memory as Memory;
use Controllers\System\Monitoring\Disk as Disk;
use Controllers\Mail;
use Exception;
use JsonException;

class Monitoring extends \Controllers\Service\Service
{
    private $monitoringController;
    private $motionServiceController;
    private $userController;

    public function __construct(string $unit)
    {
        parent::__construct($unit);

        $this->monitoringController = new \Controllers\System\Monitoring\Monitoring();
        $this->motionServiceController = new \Controllers\Motion\Service();
        $this->userController = new \Controllers\User\User();
    }

    /**
     *  Monitor CPU, memory and disk usage and log it
     */
    public function monitor() : void
    {
        parent::log('Starting system monitoring');

        while (true) {
            $currentSecond = date('s');
            $cpuUsage      = Cpu::getUsage();
            $memoryUsage   = Memory::getUsage();
            $diskUsage     = Disk::getUsage('/');

            // Create resources/monitoring/ directory if not exists
            if (!is_dir(ROOT . '/public/resources/monitoring/')) {
                if (!mkdir(ROOT . '/public/resources/monitoring/', 0755, true)) {
                    parent::logError('Could not create monitoring resources directory');
                }
            }

            // Write to file
            if (!file_put_contents(ROOT . '/public/resources/monitoring/cpu-usage', $cpuUsage)) {
                parent::logError('Could not write CPU usage to file');
            }

            if (!file_put_contents(ROOT . '/public/resources/monitoring/memory-usage', $memoryUsage)) {
                parent::logError('Could not write memory usage to file');
            }

            if (!file_put_contents(ROOT . '/public/resources/monitoring/disk-usage', $diskUsage)) {
                parent::logError('Could not write disk usage to file');
            }

            // If interval is :00 (every minute), log to databse
            if ($currentSecond == '00') {
                parent::log('Logging system monitoring data to database');
                // Add to database
                $this->monitoringController->set($cpuUsage, $memoryUsage, $diskUsage);

                // Delete old monitoring data (older than 30 days)
                $this->monitoringController->clean(30);
            }

            // Sleep to the next 10 second interval
            $sleepTime = 10 - (time() % 10);
            sleep($sleepTime);
        }
    }

    /**
     *  Monitor motion service status and log it
     */
    public function motionStatus() : void
    {
        parent::log('Logging motion service status');

        $status = 'inactive';

        if ($this->motionServiceController->isRunning() === true) {
            $status = 'active';
        }

        $this->motionServiceController->setStatusInDb($status);
    }

    /**
     *  Monitor camera status and log it
     */
    public function cameraStatus() : void
    {
        parent::log('Logging camera status');

        $cameraController = new \Controllers\Camera\Camera();
        $streamController = new \Controllers\Camera\Stream();

        try {
            // Get all cameras
            $cameras = $cameraController->getCamerasIds();

            foreach ($cameras as $id) {
                $auth = '';
                $errors = [];
                $mainStreamStatus = -1;      // Default: no stream configured
                $secondaryStreamStatus = -1; // Default: no stream configured
                $mainStreamError = '';
                $secondaryStreamError = '';

                // Get camera configuration
                $configuration = $cameraController->getConfiguration($id);

                // Decode the configuration
                try {
                    $configuration = json_decode($configuration['Configuration'], true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                    throw new Exception('Could not decode camera #' . $id . ' configuration from JSON');
                }

                // If monitoring is not enabled for this camera, skip it
                if ($configuration['monitoring']['enable'] != 'true') {
                    parent::logDebug('Skipping camera #' . $id . ' (' . $name . ') as monitoring is disabled');
                    continue;
                }

                // Get details
                $name = $configuration['name'];
                $mainStream = $configuration['main-stream']['device'] ?? '';
                $secondaryStream = $configuration['secondary-stream']['device'] ?? '';
                $username = $configuration['authentication']['username'] ?? '';
                $password = $configuration['authentication']['password'] ?? '';
                $recipients = $configuration['monitoring']['recipients'];

                // Build URL
                if (!empty($username) and !empty($password)) {
                    $auth = $username . ':' . $password . '@';
                }

                if (!empty($mainStream)) {
                    // Add auth to the URL
                    $mainStreamUrl = preg_replace('/^(rtsp|http|https):\/\//', '$1://' . $auth, $mainStream);

                    parent::logDebug('Checking main stream URL: ' . $mainStreamUrl);

                    // Check stream status
                    $status = $streamController->isActive($mainStreamUrl);

                    // If camera is reachable, set status to 1
                    if ($status === true) {
                        $mainStreamStatus = 1;
                    // If camera is not reachable, log the error
                    } else {
                        $mainStreamStatus = 0;
                        $mainStreamError = $status;

                        // Sanitize error message
                        $mainStreamErrorSanitized = preg_replace('/(rtsp|https?):\/\/[^:@\s]+:[^@\s]+@/', '$1://****:****@', $mainStreamError);
                        $mainStreamErrorSanitized = preg_replace('/(rtsp|https?):\/\/([^@\/\s]+@)?([^\/\s]+)(\/\S*)/', '$1://$2$3/****', $mainStreamErrorSanitized);

                        parent::logError('Camera #' . $id . ' (' . $name . ') main stream error: ' . $mainStreamError);

                        $errors[] = 'Main stream error:<br>' . $mainStreamErrorSanitized;
                    }
                }

                if (!empty($secondaryStream)) {
                    // Add auth to the URL
                    $secondaryStreamUrl = preg_replace('/^(rtsp|http|https):\/\//', '$1://' . $auth, $secondaryStream);

                    parent::logDebug('Checking secondary stream URL: ' . $secondaryStreamUrl);

                    // Check stream status
                    $status = $streamController->isActive($secondaryStreamUrl);

                    // If camera is reachable, set status to 1
                    if ($status === true) {
                        $secondaryStreamStatus = 1;
                    // If camera is not reachable, log the error
                    } else {
                        $secondaryStreamStatus = 0;
                        $secondaryStreamError = $status;

                        // Sanitize error message
                        $secondaryStreamErrorSanitized = preg_replace('/(rtsp|https?):\/\/[^:@\s]+:[^@\s]+@/', '$1://****:****@', $secondaryStreamError);
                        $secondaryStreamErrorSanitized = preg_replace('/(rtsp|https?):\/\/([^@\/\s]+@)?([^\/\s]+)(\/\S*)/', '$1://$2$3/****', $secondaryStreamErrorSanitized);

                        parent::logError('Camera #' . $id . ' (' . $name . ') secondary stream error: ' . $secondaryStreamError);

                        $errors[] = 'Secondary stream error:<br>' . $secondaryStreamErrorSanitized;
                    }
                }

                /**
                 *  Get latest status from database
                 */
                $latestStatus = $streamController->getLatestStatus($id);

                /**
                 *  Send a mail if there are errors
                 */
                if (!empty($errors)) {
                    $sendMail = true;

                    // If latest status of the stream(s) was the same, do not send another mail
                    if (!empty($latestStatus)) {
                        if ($latestStatus['Main_stream_status'] == $mainStreamStatus and $latestStatus['Secondary_stream_status'] == $secondaryStreamStatus) {
                            $sendMail = false;

                            parent::logDebug('Not sending email for camera #' . $id . ' (' . $name . ') as the latest status is the same');
                        }
                    }

                    if ($sendMail) {
                        // Get users
                        $users = $this->userController->getUsers();

                        if (empty($recipients)) {
                            parent::logDebug('No recipient is configured for camera #' . $id . ' (' . $name . ') monitoring, skipping email sending');
                            continue;
                        }

                        parent::logDebug('Sending email for camera #' . $id . ' (' . $name . ') as there are errors');

                        $subject = 'Error detected on ' . $name . ' camera';
                        $message = 'The following error(s) were detected on <b>' . $name . '</b> camera.<br><br>' . implode('<br><br>', $errors) . "<br><br>Please check the camera's configuration and status.";
                        new Mail($recipients, $subject, $message, __SERVER_PROTOCOL__ . '://' . WWW_HOSTNAME, 'Live stream');
                    }
                }

                /**
                 *  Add status and errors in database
                 */
                $streamController->setStatus($id, $mainStreamStatus, $secondaryStreamStatus, $mainStreamError, $secondaryStreamError);
            }
        } catch (Exception $e) {
            parent::logError('Error while monitoring camera status: ' . $e->getMessage());
        }

        parent::log('Camera status monitoring completed');

        unset($cameraController, $streamController, $cameras, $name, $id, $auth, $errors, $configuration, $mainStream, $secondaryStream, $username, $password, $mainStreamUrl, $secondaryStreamUrl, $status, $mainStreamError, $secondaryStreamError, $mainStreamStatus, $secondaryStreamStatus, $latestStatus, $sendMail, $recipients, $users, $user, $subject, $message);
    }
}
