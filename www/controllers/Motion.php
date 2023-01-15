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
    public function getStatus()
    {
        $status = trim(shell_exec('systemctl is-active motion'));

        if ($status == 'active') {
            return 'active';
        }

        return 'inactive';
    }

    /**
     *  Returns status of systemd 'motionui' service
     */
    public function getMotionUIServiceStatus()
    {
        $status = trim(shell_exec('systemctl is-active motionui'));

        if ($status == 'active') {
            return 'active';
        }

        return 'inactive';
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

        /**
         *  Get path to the file from its Id
         */
        $filePath = $this->model->getEventFilePath($fileId);

        if (empty($filePath)) {
            throw new Exception('Cannot find the specified file.');
        }

        /**
         *  Generate symlin name from filename
         */
        $symlinkName = basename($filePath);

        /**
         *  Generate symlink path
         */
        $symlinkPath = EVENTS_PICTURES . '/' . $symlinkName;

        /**
         *  Create a symlink to the real file, if not already exist
         */
        if (!file_exists($symlinkPath)) {
            symlink($filePath, $symlinkPath);
        }

        /**
         *  Finaly, check if symlink content is readable
         */
        if (!is_readable($symlinkPath)) {
            throw new Exception('Cannot read file - permission denied.');
        }

        /**
         *  Return symlink name, it will be used to visualize or download the file
         */
        return $symlinkName;
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

        if (!chgrp(DATA_DIR . '/.muttrc', 'motionui')) {
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
     *  Edit motion configuration (in /etc/motion/)
     */
    public function configure(string $filename, array $options)
    {
        $filename = \Controllers\Common::validateData($filename);
        $content = '';

        foreach ($options as $option) {
            $optionStatus = \Controllers\Common::validateData($option['status']);
            $optionName = \Controllers\Common::validateData($option['name']);
            $optionValue = $option['value'];

            /**
             *  Comment the parameter with a semicolon in the final file if status sent is not 'enabled'
             */
            if ($optionStatus == 'enabled') {
                $optionStatus = '';
            } else {
                $optionStatus = ';';
            }

            /**
             *  Check that option name is valid and does not contains invalid caracters
             */
            if (\Controllers\Common::isAlphanumDash($optionName) === false) {
                throw new Exception("<b>$optionName</b> parameter value contains invalid caracter(s)");
            }

            if (\Controllers\Common::isAlphanumDash($optionValue, array('.', ' ', ',', ':', '/', '\\', '%', '(', ')', '=', '\'', '[', ']', '@', '$')) === false) {
                throw new Exception("<b>$optionName</b> parameter value contains invalid caracter(s)");
            }

            /**
             *  Si il n'y a pas eu d'erreurs jusque là alors on forge la ligne du paramètre avec son nom et sa valeur, séparés par un égal '='
             *  Sinon on forge la même ligne mais en laissant la valeur vide afin que l'utilisateur puisse la resaisir
             */
            $content .= $optionStatus . $optionName . " " . $optionValue . PHP_EOL . PHP_EOL;
        }

        /**
         *  Write to file
         */
        if (file_exists('/etc/motion/' . $filename)) {
            file_put_contents('/etc/motion/' . $filename, $content);
        }
        unset($content);
    }

    /**
     *  Duplicate motion configuration file
     */
    public function duplicateConf(string $filename)
    {
        /**
         *  Check that specified source file exist
         */
        if (!file_exists('/etc/motion/' . $filename)) {
            throw new Exception('Specified file does not exist.');
        }

        /**
         *  Check that file is readable
         */
        if (!is_readable('/etc/motion/' . $filename)) {
            throw new Exception('Specified file is not readable.');
        }

        /**
         *  Generate a new file name
         */
        $newFileName = \Controllers\Common::generateRandom() . '-' . $filename;

        /**
         *  Regenerate name if already exist
         */
        while (file_exists('/etc/motion/' . $newFileName)) {
            $newFileName = \Controllers\Common::generateRandom() . '-' . $filename;
        }

        /**
         *  Copy source file to new file
         */
        if (!copy('/etc/motion/' . $filename, '/etc/motion/' . $newFileName)) {
            throw new Exception('Error while trying to duplicate ' . $filename);
        }
    }

    /**
     *  Delete motion configuration file
     */
    public function deleteConf(string $filename)
    {
        /**
         *  Check that specified file exist
         */
        if (!file_exists('/etc/motion/' . $filename)) {
            throw new Exception('Specified file does not exist.');
        }

        /**
         *  Check that file is writable
         */
        if (!is_writable('/etc/motion/' . $filename)) {
            throw new Exception('Specified file is not writable.');
        }

        /**
         *  Delete file
         */
        if (!unlink('/etc/motion/' . $filename)) {
            throw new Exception('Error while trying to delete ' . $filename);
        }
    }

    /**
     *  Rename motion configuration file
     */
    public function renameConf(string $filename, string $newName)
    {
        /**
         *  Check that the new name is valid and does not contains invalid caracters
         */
        if (\Controllers\Common::isAlphanumDash($newName, array('.')) === false) {
            throw new Exception("Specified new name <b>$newName</b> is not valid.");
        }

        /**
         *  Check that a file does not already exist with the same name
         */
        if (file_exists('/etc/motion/' . $newName)) {
            throw new Exception('A file with the same name <b>' . $newName . '</b> already exists.');
        }

        /**
         *  Check that a file ends with .conf
         */
        if (!preg_match('/.conf$/', $newName)) {
            throw new Exception('File must end with .conf');
        }

        /**
         *  Check that the file is writable
         */
        if (!is_writable('/etc/motion/' . $filename)) {
            throw new Exception('Specified file is not writable.');
        }

        /**
         *  Rename file
         */
        if (!rename('/etc/motion/' . $filename, '/etc/motion/' . $newName)) {
            throw new Exception('Error while trying to rename ' . $filename);
        }
    }

    /**
     *  Set up event registering in specified configuration file
     */
    public function setUpEvent(string $filename)
    {
        $filename = Common::validateData($filename);

        if (!file_exists('/etc/motion/' . $filename)) {
            throw new Exception('Specified configuratin file does not exist <b>' . $filename . '</b>');
        }

        if (!is_writable('/etc/motion/' . $filename)) {
            throw new Exception('Configuration file <b>' . $filename . '</b> is not writable');
        }

        $content = file_get_contents('/etc/motion/' . $filename);

        /**
         *  Check if 'on_event_start' param is present in the file
         *  If so, remove it
         *  Then just add the parameter with its new value
         *
         *  Regex:
         *  ;?    => matches ';' if there are
         *  \s*?  => matches one or more white spaces if there are
         */
        if (preg_match('/;?\s*?on_event_start.*/i', $content)) {
            $content = preg_replace('/;?\s*?on_event_start.*/i', PHP_EOL, $content);
        }
        /**
         *  Also check if 'camera_name' param is present and uncomment it if it is commented
         */
        if (preg_match('/;?\s*?camera_name.*/i', $content)) {
            $content = preg_replace('/;?\s*?camera_name/i', 'camera_name', $content);

            /**
             *  --cam-name is added if 'camera_name' param is present
             */
            $content .= PHP_EOL . 'on_event_start /var/lib/motionui/tools/event --cam-id %t --cam-name %$ --register-event %v' . PHP_EOL;
        } else {
            $content .= PHP_EOL . 'on_event_start /var/lib/motionui/tools/event --cam-id %t --register-event %v' . PHP_EOL;
        }

        /**
         *  Check if 'on_event_end' param is present in the file
         *  If so, remove it
         *  Then just add the parameter with its new value
         */
        if (preg_match('/;?\s*?on_event_end.*/i', $content)) {
            $content = preg_replace('/;?\s*?on_event_end.*/i', PHP_EOL, $content);
        }
        $content .= PHP_EOL . 'on_event_end /var/lib/motionui/tools/event --cam-id %t --end-event %v' . PHP_EOL;

        /**
         *  Check if 'on_movie_end' param is present in the file
         *  If so, remove it
         *  Then just add the parameter with its new value
         */
        if (preg_match('/;?\s*?on_movie_end.*/i', $content)) {
            $content = preg_replace('/;?\s*?on_movie_end(.*)/i', PHP_EOL, $content);
        }
        $content .= PHP_EOL . 'on_movie_end /var/lib/motionui/tools/event --cam-id %t --event %v --file %f' . PHP_EOL;

        /**
         *  Write content to the file
         */
        if (!file_put_contents('/etc/motion/' . $filename, $content)) {
            throw new Exception('Error while writing to <b>/etc/motion/' . $filename . '</b>');
        }
    }
}
