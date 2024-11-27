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
    public function addStream(int $id, array $params)
    {
        $id = $params['id'];
        $urlOrDevice = $params['url'];
        $basicAuthUsername = $params['basicAuthUsername'];
        $basicAuthPassword = $params['basicAuthPassword'];
        $rotate = $params['rotate'];
        $resolution = $params['resolution'];
        $frameRate = $params['framerate'];
        $hardwareAcceleration = $params['hardware_acceleration'];
        $ffmpegParams = '#video=mjpeg';

        /**
         *  Rotate filter
         */
        if ($rotate == 90) {
            $ffmpegParams .= '#rotate=90';
        } else if ($rotate == 180) {
            $ffmpegParams .= '#rotate=180';
        } else if ($rotate == 270) {
            $ffmpegParams .= '#rotate=270';
        }

        /**
         *  Frame rate
         *  If framerate is 0, then we use the default frame rate of the camera, otherwise we set it
         */
        if ($params['framerate'] > 0) {
            $ffmpegParams .= '#raw=-r ' . $params['framerate'];
        }

        /**
         *  Enable hardware acceleration
         */
        if ($hardwareAcceleration == 'true') {
            $ffmpegParams .= '#hardware';
        }

        /**
         *  Trim ffmpeg params
         */
        $ffmpegParams = trim($ffmpegParams);

        /**
         *  First, get actual config
         */
        $config = $this->getConfig();

        /**
         *  Detect stream type, from url / device
         */
        if (preg_match('#^rtsps?://#', $urlOrDevice)) {
            /**
             *  If camera has username and password, add it to the URL (format is http://username:password@url)
             */
            if (!empty($basicAuthUsername) and !empty($basicAuthPassword)) {
                $urlOrDevice = preg_replace('#://#i', '://' . $basicAuthUsername . ':' . $basicAuthPassword . '@', $urlOrDevice);
            }

            // Define go2rtc stream command
            $stream = 'ffmpeg:' . $urlOrDevice . $ffmpegParams;
        } else if (preg_match('#^https?://#', $urlOrDevice)) {
            /**
             *  If camera has username and password, add it to the URL (format is http://username:password@url)
             */
            if (!empty($basicAuthUsername) and !empty($basicAuthPassword)) {
                $urlOrDevice = preg_replace('#://#i', '://' . $basicAuthUsername . ':' . $basicAuthPassword . '@', $urlOrDevice);
            }

            /**
             *  If a rotate > 0, then we need to use ffmpeg to rotate the stream
             *  If framerate > 0, then we need to use ffmpeg to set the frame rate
             *  Otherwise, we could use the stream directly with no modifications
             */
            if ($rotate > 0 or $frameRate > 0) {
                $stream = 'ffmpeg:' . $urlOrDevice . $ffmpegParams;
            } else {
                $stream = $urlOrDevice;
            }
        } else if (preg_match('#^/dev/video#', $urlOrDevice)) {
            // Define go2rtc stream command
            $stream = 'ffmpeg:' . $urlOrDevice . $ffmpegParams;
        } else {
            throw new Exception('Unknown stream type for URL or device ' . $urlOrDevice);
        }

        /**
         *  Add stream in config
         */
        $config['streams']['camera_' . $id] = $stream;
        $config['api']['mjpeg'][] = 'camera_' . $id;

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
        $config['api']['mjpeg'] = array_diff($config['api']['mjpeg'], ['camera_' . $id]);

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
    public function editStream(int $id, array $params)
    {
        /**
         *  Remove and add stream
         *  Restart go2rtc only once
         */
        $this->removeStream($id, false);
        $this->addStream($id, $params);
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
        $myprocess = new \Controllers\Process('/usr/local/bin/go2rtc -c ' . GO2RTC_DIR . '/go2rtc.yml >/dev/null 2>/dev/null &');
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
