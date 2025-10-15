<?php

namespace Controllers\Go2rtc;

use Exception;

class Go2rtc
{
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
            // if (!in_array('camera_' . $id, $config['api']['mjpeg'])) {
            //     $config['api']['mjpeg'][] = 'camera_' . $id;
            // }
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
        $myprocess = new \Controllers\Process('/usr/sbin/service go2rtc restart');
        $myprocess->execute();
        $output = $myprocess->getOutput();
        $myprocess->close();

        if ($myprocess->getExitCode() != 0) {
            throw new Exception('Failed to restart go2rtc:<br><pre class="codeblock">' . $output . '</pre>');
        }
    }

    /**
     *  Return go2rtc log content
     */
    public function getLog(string $log) : string
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to view go2rtc logs');
        }

        $log = realpath(LOGS_DIR . '/go2rtc/' . $log);

        if (!preg_match('#^' . LOGS_DIR . '/go2rtc/.*log#', $log)) {
            throw new Exception('Invalid log file');
        }

        if (!file_exists($log)) {
            throw new Exception('Log file does not exist');
        }

        $content = file_get_contents($log);

        if ($content === false) {
            throw new Exception('Failed to read log file');
        }

        return $content;
    }
}
