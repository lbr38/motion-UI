<?php

namespace Controllers\Go2rtc;

use Exception;

class Go2rtc
{
    public function __construct()
    {
        /**
         *  Generate config file for go2rtc, if not exists
         */
        if (!file_exists(DATA_DIR . '/go2rtc/go2rtc.yml')) {
            if (!copy(ROOT . '/templates/go2rtc/go2rtc.yml', GO2RTC_DIR . '/go2rtc.yml')) {
                throw new Exception('Failed to generate go2rtc.yml config file');
            }

            /**
             *  Restart go2rtc
             */
            $this->restart();
        }
    }

    /**
     *  Return go2rtc config as array
     */
    private function getConfig() : array
    {
        $config = yaml_parse_file(GO2RTC_DIR . '/go2rtc.yml');

        if ($config === false) {
            throw new Exception('Failed to parse go2rtc config file');
        }

        return $config;
    }

    /**
     *  Add a new stream to go2rtc
     */
    public function addStream(int $id, array $streamUrls)
    {
        /**
         *  First, get actual config
         */
        $config = $this->getConfig();

        /**
         *  Add stream in config
         */
        foreach ($streamUrls as $url) {
            $config['streams']['camera_' . $id][] = htmlspecialchars_decode($url);
            // $config['api']['mjpeg'][] = 'camera_' . $id;
        }

        /**
         *  Save config
         */
        if (!yaml_emit_file(GO2RTC_DIR . '/go2rtc.yml', $config)) {
            throw new Exception('Failed to save go2rtc config file');
        }

        /**
         *  Restart go2rtc
         */
        $this->restart();
    }

    /**
     *  Remove a stream from go2rtc
     */
    public function removeStream(int $id, bool $restart = true)
    {
        /**
         *  First, get actual config
         */
        $config = $this->getConfig();

        /**
         *  Remove stream from config
         */
        unset($config['streams']['camera_' . $id]);

        /**
         *  Remove stream from api mjpeg
         */
        if (isset($config['api']['mjpeg'])) {
            $config['api']['mjpeg'] = array_diff($config['api']['mjpeg'], ['camera_' . $id]);
        }

        /**
         *  Save config
         */
        if (!yaml_emit_file(GO2RTC_DIR . '/go2rtc.yml', $config)) {
            throw new Exception('Failed to save go2rtc config file');
        }

        /**
         *  Restart go2rtc
         */
        if ($restart) {
            $this->restart();
        }
    }

    /**
     *  Edit a stream in go2rtc
     */
    public function editStream(int $id, array $streamUrls)
    {
        /**
         *  Remove and add stream
         *  Restart go2rtc only once
         */
        $this->removeStream($id, false);
        $this->addStream($id, $streamUrls);
    }

    /**
     *  Restart go2rtc
     */
    public function restart()
    {
        /**
         *  Check if go2rtc is running
         */
        $myprocess = new \Controllers\Process('/usr/bin/ps aux | grep "go2rtc" | grep -v grep');
        $myprocess->execute();
        $myprocess->close();

        /**
         *  If go2rtc is running, kill it
         */
        if ($myprocess->getExitCode() == 0) {
            $myprocess = new \Controllers\Process('/usr/bin/killall go2rtc');
            $myprocess->execute();
            $myprocess->close();

            if ($myprocess->getExitCode() != 0) {
                throw new Exception('Failed to stop go2rtc');
            }
        }

        /**
         *  Start go2rtc in background
         */
        $myprocess = new \Controllers\Process('/usr/local/bin/go2rtc -c ' . GO2RTC_DIR . '/go2rtc.yml > /var/lib/motionui/go2rtc/go2rtc.log 2>&1 &');
        $myprocess->execute();
        $myprocess->close();

        if ($myprocess->getExitCode() != 0) {
            throw new Exception('Failed to start go2rtc');
        }

        /**
         *  Check if go2rtc is running
         */
        $myprocess = new \Controllers\Process('/usr/bin/ps aux | grep "go2rtc" | grep -v grep');
        $myprocess->execute();
        $myprocess->close();

        if ($myprocess->getExitCode() != 0) {
            throw new Exception('Failed to start go2rtc (no process found)');
        }
    }
}
