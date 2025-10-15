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
                $mainStreamStatus = -1; // Default: no stream configured
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

                // Get details
                $mainStream = $configuration['main-stream']['device'] ?? '';
                $secondaryStream = $configuration['secondary-stream']['device'] ?? '';
                $username = $configuration['authentication']['username'] ?? '';
                $password = $configuration['authentication']['password'] ?? '';

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
                        parent::logError('Camera #' . $id . ' main stream error: ' . $mainStreamError);
                        $errors[] = 'Main stream error: ' . $mainStreamError;
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
                        parent::logError('Camera #' . $id . ' secondary stream error: ' . $secondaryStreamError);
                        $errors[] = 'Secondary stream error: ' . $secondaryStreamError;
                    }
                }

                /**
                 *  Get latest status from database
                 */
                $latestStatus = $streamController->getLatestStatus($id);

                /**
                 *  Add status and errors in database
                 */
                $streamController->setStatus($id, $mainStreamStatus, $secondaryStreamStatus, $mainStreamError, $secondaryStreamError);

                /**
                 *  Send a mail if there are errors
                 */
                if (!empty($errors)) {
                    $sendMail = true;

                    // If latest status of the stream(s) was the same, do not send another mail
                    if (!empty($latestStatus)) {
                        if ($latestStatus['Main_stream_status'] == $mainStreamStatus and $latestStatus['Secondary_stream_status'] == $secondaryStreamStatus) {
                            $sendMail = false;

                            // TODO debug
                            parent::log('Not sending mail for camera #' . $id . ' as the latest status is the same');
                        }
                    }

                    if ($sendMail) {
                        $recipients = [];

                        // Get users
                        $users = $this->userController->getUsers();

                        // Only send alert to admin users for now
                        foreach ($users as $user) {
                            if (!in_array($user['Role_name'], ['super-administrator', 'administrator'])) {
                                continue;
                            }

                            // Add user email to recipients list if email is not empty
                            if (!empty($user['Email'])) {
                                $recipients[] = $user['Email'];
                            }
                        }

                        // TODO send mail
                        parent::log('Sending mail for camera #' . $id . ' as there are errors');

                        if (empty($recipients)) {
                            parent::logDebug('No recipient found for camera #' . $id . ' alert mail');
                            continue;
                        }

                        $subject = 'Camera #' . $id . ' monitoring';
                        $message = 'The following error(s) were detected on camera #' . $id . ":\n\n" . implode("\n", $errors) . "\n\nPlease check the camera configuration and the camera connection.";
                        // new Mail('xxx', $subject, $message);
                        new Mail($recipients, $subject, $message, __SERVER_PROTOCOL__ . '://' . WWW_HOSTNAME, 'Live stream');
                    }
                }
            }
        } catch (Exception $e) {
            parent::logError('Error while monitoring camera status: ' . $e->getMessage());
        }

        unset($cameraController, $streamController, $cameras, $id, $auth, $errors, $configuration, $mainStream, $secondaryStream, $username, $password, $mainStreamUrl, $secondaryStreamUrl, $status, $mainStreamError, $secondaryStreamError, $mainStreamStatus, $secondaryStreamStatus, $latestStatus, $sendMail, $recipients, $users, $user, $subject, $message);
    }
}
