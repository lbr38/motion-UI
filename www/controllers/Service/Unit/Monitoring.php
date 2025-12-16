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
        parent::log('Logging system usage');

        $cpuUsage    = Cpu::getUsage();
        $memoryUsage = Memory::getUsage();
        $diskUsage   = Disk::getUsage('/');

        // Add to database
        $this->monitoringController->set($cpuUsage, $memoryUsage, $diskUsage);

        // Delete old monitoring data (older than 30 days)
        $this->monitoringController->clean(30);
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
                    parent::logDebug('Skipping camera #' . $id . ' as monitoring is disabled');
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

                    // Sanitize URL for logging
                    $mainStreamSanitized = preg_replace('/^(rtsp|http|https):\/\//', '$1://' . '****:****@', $mainStream);

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
                        parent::logError('Camera #' . $id . ' main stream error: ' . $mainStreamError);

                        $errors[] = 'Main stream error: ' . $mainStreamSanitized;
                    }
                }

                if (!empty($secondaryStream)) {
                    // Add auth to the URL
                    $secondaryStreamUrl = preg_replace('/^(rtsp|http|https):\/\//', '$1://' . $auth, $secondaryStream);

                    // Sanitize URL for logging
                    $secondaryStreamSanitized = preg_replace('/^(rtsp|http|https):\/\//', '$1://' . '****:****@', $secondaryStream);

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
                        parent::logError('Camera #' . $id . ' secondary stream error: ' . $secondaryStreamError);

                        $errors[] = 'Secondary stream error: ' . $secondaryStreamSanitized;
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

                            parent::logDebug('Not sending email for camera #' . $id . ' as the latest status is the same');
                        }
                    }

                    if ($sendMail) {
                        // Get users
                        $users = $this->userController->getUsers();

                        parent::logDebug('Sending email for camera #' . $id . ' as there are errors');

                        if (empty($recipients)) {
                            parent::logDebug('No recipient is configured for camera #' . $id . ' monitoring');
                            continue;
                        }

                        $subject = 'Error detected on ' . $name . ' camera';
                        $message = 'The following error(s) were detected on <b>' . $name . '</b> camera:<br><br>' . implode('<br><br>', $errors) . '<br><br>Please check the camera(s) configuration and connection.';
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
