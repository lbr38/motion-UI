<?php

namespace Controllers;

use Exception;

class Motion
{
    private $model;

    public function __construct()
    {
        $this->model = new \Models\Motion();
    }

    /**
     *  Returns motion service status
     */
    public function motionServiceRunning()
    {
        $status = trim(shell_exec('systemctl is-active motion'));

        if ($status == 'active') {
            return true;
        }

        return false;
    }

    /**
     *  Returns status of systemd 'motionui' service
     */
    public function motionuiServiceRunning()
    {
        $status = trim(shell_exec('systemctl is-active motionui'));

        if ($status != 'active') {
            return false;
        }

        return true;
    }

    /**
     *  Returns actual autostart time slots configuration
     */
    public function getAutostartConfiguration()
    {
        /**
         *  Get actual configuration
         */
        $configuration = $this->model->getAutostartConfiguration();

        return $configuration;
    }

    /**
     *  Returns autostart parameter status
     */
    public function getAutostartStatus()
    {
        $status = $this->getAutostartConfiguration();

        return $status['Status'];
    }

    /**
     *  Returns autostart on device presence parameter status
     */
    public function getAutostartOnDevicePresenceStatus()
    {
        $status = $this->getAutostartConfiguration();

        return $status['Device_presence'];
    }

    /**
     *  Returns known devices
     */
    public function getAutostartDevices()
    {
        return $this->model->getAutostartDevices();
    }

    /**
     *  Returns actual alerts time slots configuration
     */
    public function getAlertConfiguration()
    {
        /**
         *  Get actual configuration
         */
        $configuration = $this->model->getAlertConfiguration();

        return $configuration;
    }

    /**
     *  Returns alert parameter status
     */
    public function getAlertStatus()
    {
        $status = $this->getAlertConfiguration();

        return $status['Status'];
    }

    /**
     *  Get total event count for the specified date
     */
    public function getDailyEventCount(string $date)
    {
        return count($this->model->getDailyEvent($date));
    }

    /**
     *  Get total files recorded count for the specified date
     */
    public function getDailyFileCount(string $date)
    {
        return count($this->model->getDailyFile($date));
    }

    /**
     *  Return events between dates
     */
    public function getEventsDate(string $dateStart, string $dateEnd)
    {
        return $this->model->getEventsDate($dateStart, $dateEnd);
    }

    /**
     *  Return events time by date
     */
    public function getEventsTime(string $date)
    {
        return $this->model->getEventsTime($date);
    }

    /**
     *  Return all events details by date and time
     */
    public function getEventsDetails(string $date, string $time)
    {
        return $this->model->getEventsDetails($date, $time);
    }

    /**
     *  Return total event count by date
     */
    public function totalEventByDate(string $date)
    {
        return count($this->model->totalEventByDate($date));
    }

    /**
     *  Return total files count from an event
     */
    public function totalFilesByEventId(string $eventId)
    {
        return count($this->model->totalFilesByEventId($eventId));
    }

    /**
     *  Generate event image or video link to visualize
     */
    public function getEventFile(string $fileId)
    {
        /**
         *  File Id must be numeric
         */
        if (!is_numeric($fileId)) {
            throw new Exception('The specified file is invalid.');
        }

        return $this->model->getEventFilePath($fileId);
    }

    /**
     *  Get daily motion service status (for stats)
     */
    public function getMotionServiceStatus()
    {
        return $this->model->getMotionServiceStatus();
    }

    /**
     *  Start / stop motion capture
     */
    public function startStop(string $status)
    {
        if ($status == 'start') {
            if (!file_exists(DATA_DIR . '/start-motion.request')) {
                touch(DATA_DIR . '/start-motion.request');
            }
        }
        if ($status == 'stop') {
            if (!file_exists(DATA_DIR . '/stop-motion.request')) {
                touch(DATA_DIR . '/stop-motion.request');
            }
        }
    }

    /**
     *  Enable / disable motion autostart
     */
    public function enableAutostart(string $status)
    {
        if ($status != 'enabled' and $status != 'disabled') {
            throw new Exception('Invalid parameter');
        }

        $this->model->enableAutostart($status);
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
    public function configureAutostart(string $mondayStart, string $mondayEnd, string $tuesdayStart, string $tuesdayEnd, string $wednesdayStart, string $wednesdayEnd, string $thursdayStart, string $thursdayEnd, string $fridayStart, string $fridayEnd, string $saturdayStart, string $saturdayEnd, string $sundayStart, string $sundayEnd)
    {
        $this->model->configureAutostart(
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
     *  Add a new device name and ip address to known devices
     */
    public function addDevice(string $name, string $ip)
    {
        $name = \Controllers\Common::validateData($name);
        $ip = \Controllers\Common::validateData($ip);

        $this->model->addDevice($name, $ip);
    }

    /**
     *  Remove a known device
     */
    public function removeDevice(int $id)
    {
        if (!is_numeric($id)) {
            throw new Exception('Invalid device id');
        }

        $this->model->removeDevice($id);
    }

    /**
     *  Enable / disable motion alerts
     */
    public function enableAlert(string $status)
    {
        if ($status != 'enabled' and $status != 'disabled') {
            throw new Exception('Invalid parameter');
        }

        $this->model->enableAlert($status);
    }

    /**
     *  Configure motion alerts
     */
    public function configureAlert(string $mondayStart, string $mondayEnd, string $tuesdayStart, string $tuesdayEnd, string $wednesdayStart, string $wednesdayEnd, string $thursdayStart, string $thursdayEnd, string $fridayStart, string $fridayEnd, string $saturdayStart, string $saturdayEnd, string $sundayStart, string $sundayEnd, string $mailRecipient)
    {
        $this->model->configureAlert(
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

    /**
     *  Generate a new muttrc template file
     */
    public function generateMuttrc()
    {
        if (file_exists(DATA_DIR . '/.muttrc')) {
            throw new Exception('Muttrc config file already exists');
        }

        /**
         *  Touching the file
         */
        if (!touch(DATA_DIR . '/.muttrc')) {
            throw new Exception('Error while creating muttrc config template');
        }

        /**
         *  Setting proper permissions on the new file
         */
        if (!chmod(DATA_DIR . '/.muttrc', octdec("0660"))) {
            throw new Exception('Error while setting permissions on ' . DATA_DIR . '/.muttrc');
        }

        if (!chgrp(DATA_DIR . '/.muttrc', 'motion')) {
            throw new Exception('Error while setting group owner on ' . DATA_DIR . '/.muttrc');
        }

        /**
         *  Adding template configuration
         */
        if (!file_put_contents(DATA_DIR . '/.muttrc', file_get_contents(ROOT . '/templates/muttrc-template'))) {
            throw new Exception('Error while generating muttrc config template');
        }
    }

    /**
     *  Save muttrc configuration
     */
    public function editMutt(string $realName, string $from, string $smtpUrl, string $smtpPassword)
    {
        $realName = Common::validateData($realName);
        $from = Common::validateData($from);
        $smtpPassword = Common::validateData($smtpPassword);
        $smtpUrl = Common::validateData($smtpUrl);

        $muttArray = array();
        $muttArray['set ssl_starttls'] = 'yes';
        $muttArray['set ssl_force_tls'] = 'yes';
        $muttArray['set use_envelope_from'] = 'yes';
        $muttArray['set copy'] = 'no';
        $muttArray['set charset'] = 'utf-8';
        $muttArray['set realname'] = $realName;
        $muttArray['set from'] = $from;
        $muttArray['set smtp_url'] = $smtpUrl;
        $muttArray['set smtp_pass'] = $smtpPassword;

        Common::writeToIni($muttArray, DATA_DIR . '/.muttrc');
    }

    /**
     *  Delete event media file(s)
     */
    public function deleteFile(array $filesId)
    {
        if (empty($filesId)) {
            throw new Exception('No file has been specified');
        }

        foreach ($filesId as $fileId) {
            /**
             *  Get file path
             */
            $filePath = $this->model->getEventFilePath($fileId);

            /**
             *  Check that file is writeable
             */
            if (!is_writeable($filePath)) {
                throw new Exception('File <b>' . $filePath . '</b> is not writeable');
            }

            /**
             *  Delete file
             */
            if (!unlink($filePath)) {
                throw new Exception('Could not delete file <b>' . $filePath . '</b>');
            }
        }
    }

    /**
     *  Edit motion configuration file (in /etc/motion/cameras/)
     */
    public function configure(string $cameraId, array $options)
    {
        $filename = CAMERAS_DIR . '/camera-' . $cameraId . '.conf';

        if (!file_exists($filename)) {
            throw new Exception('Camera configuration file does not exist: ' . $filename);
        }

        $content = '';

        foreach ($options as $option) {
            /**
             *  Comment the parameter with a semicolon in the final file if status sent is not 'enabled'
             */
            if ($option['status'] == 'enabled') {
                $optionStatus = '';
            } else {
                $optionStatus = ';';
            }

            /**
             *  Check that option name is valid and does not contains invalid caracters
             */
            if (\Controllers\Common::isAlphanumDash($option['name']) === false) {
                throw new Exception('<b>' . $option['name'] . '</b> parameter name contains invalid caracter(s)');
            }

            if (\Controllers\Common::isAlphanumDash($option['value'], array('&', '?', '.', ' ', ',', ':', '/', '%', '(', ')', '=', '\'', '[', ']', '@')) === false) {
                throw new Exception('<b>' . $option['name'] . '</b> parameter value contains invalid caracter(s)');
            }

            $optionName = \Controllers\Common::validateData($option['name']);
            $optionValue = $option['value'];

            /**
             *  Si il n'y a pas eu d'erreurs jusque là alors on forge la ligne du paramètre avec son nom et sa valeur, séparés par un égal '='
             *  Sinon on forge la même ligne mais en laissant la valeur vide afin que l'utilisateur puisse la resaisir
             */
            $content .= $optionStatus . $optionName . " " . $optionValue . PHP_EOL . PHP_EOL;
        }

        /**
         *  Write to file
         */
        if (file_exists($filename)) {
            file_put_contents($filename, trim($content));
        }

        unset($content);

        /**
         *  Restart motion service if running
         */
        if ($this->motionServiceRunning()) {
            if (!file_exists(DATA_DIR . '/motion.restart')) {
                touch(DATA_DIR . '/motion.restart');
            }
        }
    }
}
