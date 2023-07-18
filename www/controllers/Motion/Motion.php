<?php

namespace Controllers\Motion;

use Exception;

class Motion
{
    private $service;

    public function __construct()
    {
        $this->service = new \Controllers\Motion\Service();
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
     *  Edit motion configuration file (in /var/lib/motionui/cameras/)
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

            if (\Controllers\Common::isAlphanumDash($option['value'], array('.', ' ', ',', ':', '/', '%Y', '%m', '%d', '%H', '%M', '%S', '%q', '%v', '%t', '%w', '%h', '%D', '%f', '%{eventid}', '%{fps}', '(', ')', '=', '\'', '[', ']', '@')) === false) {
                throw new Exception('<b>' . $option['name'] . '</b> parameter value contains invalid caracter(s)');
            }

            $optionName = \Controllers\Common::validateData($option['name']);
            $optionValue = $option['value'];

            /**
             *  If there is no error then forge the parameter line with its name and value, separated by a space ' '
             *  Else forge the same line but leave the value empty so that the user can re-enter it
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
        if ($this->service->isRunning()) {
            if (!file_exists(DATA_DIR . '/motion.restart')) {
                touch(DATA_DIR . '/motion.restart');
            }
        }
    }
}
