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
    public function configureAlert(string $mondayStart, string $mondayEnd, string $tuesdayStart, string $tuesdayEnd, string $wednesdayStart, string $wednesdayEnd, string $thursdayStart, string $thursdayEnd, string $fridayStart, string $fridayEnd, string $saturdayStart, string $saturdayEnd, string $sundayStart, string $sundayEnd, string $mailRecipient, string $muttConfig)
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
            \Controllers\Common::validateData($mailRecipient),
            \Controllers\Common::validateData($muttConfig)
        );
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
             *  On vérifie que le nom de l'option est valide, càd qu'il ne contient pas de caractère spéciaux
             */
            if (\Controllers\Common::isAlphanumDash($optionName) === false) {
                throw new Exception("<b>$optionName</b> parameter value contains invalid caracter(s)");
            }

            if (\Controllers\Common::isAlphanumDash($optionValue, array('.', ' ', ',', ':', '/', '\\', '%', '(', ')', '=', '\'', '[', ']', '@')) === false) {
                throw new Exception("<b>$optionName</b> parameter value contains invalid caracter(s)");
            }

            /**
             *  Si il n'y a pas eu d'erreurs jusque là alors on forge la ligne du paramètre avec son nom et sa valeur, séparés par un égal '='
             *  Sinon on forge la même ligne mais en laissant la valeur vide afin que l'utilisateur puisse la resaisir
             */
            $content .= $optionStatus . $optionName . " " . $optionValue . PHP_EOL . PHP_EOL;
        }

        /**
         *  Enfin, on écrit le contenu dans le fichier spécifié
         */
        if (file_exists('/etc/motion/' . $filename)) {
            file_put_contents('/etc/motion/' . $filename, $content);
        }
        unset($content);
    }
}
