<?php

namespace Controllers\Motion;

use Exception;

class Event
{
    private $model;
    private $layoutContainerReloadController;
    private $logController;

    public function __construct()
    {
        $this->model = new \Models\Motion\Event();
        $this->layoutContainerReloadController = new \Controllers\Layout\ContainerReload();
        $this->logController = new \Controllers\Log\Log();
    }

    /**
     *  Add a new event to database
     */
    public function new(string $motionEventId, int $motionEventIdShort, int $cameraId)
    {
        $dateStart = date('Y-m-d');
        $timeStart = date('H:i:s');

        /**
         *  Check if event already exists
         */
        if ($this->exists($motionEventId) === true) {
            return;
        }

        /**
         *  Add event to database
         */
        $this->model->new($motionEventId, $motionEventIdShort, $dateStart, $timeStart, $cameraId);

        /**
         *  Send mail alert, if enabled
         */
        if (ALERT_ENABLED == 'enabled') {
            $this->alert('event', $dateStart, $timeStart, $motionEventId);
        }

        /**
         *  Refresh page
         */
        $this->layoutContainerReloadController->reload('motion/events/list');
        $this->layoutContainerReloadController->reload('buttons/bottom');
    }

    /**
     *  End an event
     */
    public function end(string $motionEventId)
    {
        $this->model->end($motionEventId);

        /**
         *  Refresh page
         */
        $this->layoutContainerReloadController->reload('motion/events/list');
        $this->layoutContainerReloadController->reload('buttons/bottom');
    }

    /**
     *  Acquit all events
     */
    public function acquitAll()
    {
        $this->model->acquitAll();
    }

    /**
     *  Mark an event as seen
     */
    public function seen(int $id)
    {
        $this->model->seen($id);
    }

    /**
     *  Return total unseen events count
     */
    public function getUnseenCount(int|null $cameraId = null)
    {
        return $this->model->getUnseenCount($cameraId);
    }

    /**
     *  Attach a file to an event
     */
    public function attachFile(string $motionEventId, string $file, int $width, int $height, int $fps, int $changed_pixels)
    {
        /**
         *  Generate date and time of the event file
         */
        $date = date('Y-m-d');
        $time = date('H:i');

        /**
         *  Check that provided file is valid (must be in captures directory)
         */
        if (!preg_match('#^' . CAPTURES_DIR . '#', realpath($file))) {
            throw new Exception('The specified file is invalid.');
        }

        /**
         *  File must be a picture or a movie
         */
        if (!preg_match('/\.(jpg|jpeg|mp4|mov|mkv|webm)$/', $file)) {
            throw new Exception('The specified file extension is not allowed.');
        }

        /**
         *  Check that MIME type is valid
         */
        if (!in_array(mime_content_type($file), ['image/jpeg', 'video/mp4', 'video/quicktime', 'video/x-matroska', 'video/webm'])) {
            throw new Exception('The specified file MIME type is not allowed.');
        }

        /**
         *  Attach file to event
         */
        $this->model->attachFile($motionEventId, $file, \Controllers\Common::sizeFormat(filesize($file), true), $width, $height, $fps, $changed_pixels);

        /**
         *  If the file is a movie, then create a thumbnail image for it
         */
        if (preg_match('/\.(mp4|mkv|mov|webm)$/', $file)) {
            /**
             *  Get file directory location
             */
            $fileDir = dirname($file);

            /**
             *  Create thumbnail if not already exist
             */
            if (!file_exists($file . '.thumbnail')) {
                /**
                 *  First, get the duration of the movie
                 */
                $myprocess = new \Controllers\Process("/usr/bin/mediainfo --Output='General;%Duration%' " . $file);
                $myprocess->execute();
                $output = $myprocess->getOutput();
                $myprocess->close();

                /**
                 *  If duration has been found, then create thumbnail
                 */
                if (!empty(trim($output))) {
                    $duration = $output;

                    /**
                     *  Duration is in milliseconds, so convert it to seconds
                     */
                    $totalSeconds = $duration / 1000;

                    /**
                     *  Create thumbnail at the middle of the movie
                     */
                    $myprocess = new \Controllers\Process('/usr/bin/ffmpeg -loglevel error -ss ' . gmdate("H:i:s", $totalSeconds / 2) . ' -i ' . $file . " -vf 'scale=320:320:force_original_aspect_ratio=decrease' -frames:v 1 -q:v 2 " . $file . '.thumbnail.jpg');
                    $myprocess->execute();
                    $output = $myprocess->getOutput();
                    $myprocess->close();
                }
            }
        }

        /**
         *  Send alert with the file attached, if enabled
         */
        if (ALERT_ENABLED == 'enabled') {
            $this->alert('file', $date, $time, $motionEventId, $file);
        }

        /**
         *  Refresh page
         */
        $this->layoutContainerReloadController->reload('motion/events/list');
    }

    /**
     *  Send a mail alert on a new motion event
     */
    private function alert(string $type, string $date, string $time, string $motionEventId, string $file = null)
    {
        $mymotionAlert = new \Controllers\Motion\Alert();
        $mycamera = new \Controllers\Camera\Camera();

        /**
         *  Get alert settings
         */
        $alertConfiguration = $mymotionAlert->getConfiguration();

        /**
         *  Get today alert configuration
         */
        $day = date('l');
        $alertTodayStart = $alertConfiguration[$day . '_start'];
        $alertTodayEnd = $alertConfiguration[$day . '_end'];
        $alertRecipient = $alertConfiguration['Recipient'];

        /**
         *  Quit if one alert parameter is empty
         */
        if (empty($alertTodayStart) || empty($alertTodayEnd)) {
            return;
        }

        /**
         *  If end time has been set at 00:00, then alert should always be send until the end of the day.
         *  So convert it to a high number to pass all conditions and be sure to send alert till the end of the day.
         */
        if ($alertTodayEnd == '00:00') {
            $alertTodayEnd = '23:59:59';
        }

        /**
         *  Get camera name from the specified event
         */
        $cameraName = $mycamera->getNameByEventId($motionEventId);
        $motionEventIdShort = $this->getEventIdShort($motionEventId);

        /**
         *  If actual time is between alert time slot, then send alert
         */
        $actualTime = time();

        if ($actualTime > strtotime($alertTodayStart) && $actualTime < strtotime($alertTodayEnd)) {
            /**
             *  Building final subject and message
             */
            if ($type == 'event') {
                $mailSubject = 'Motion-UI - ' . $cameraName . ' - event #' . $motionEventIdShort . ' - New motion detected';
                $mailMessage = '<p><b>Camera</b>: ' . $cameraName . '<br>';
                $mailMessage .= '<b>Event</b>: #' . $motionEventIdShort . ' (#' . $motionEventId . ')<br>';
                $mailMessage .= '<b>Date</b>: ' . $date . ' ' . $time . '<br><br></p>';
                $mailMessage .= '<p>A new motion has been detected by this camera.<br></p>';
                $mymail = new \Controllers\Mail($alertRecipient, $mailSubject, $mailMessage, 'http://' . WWW_HOSTNAME . '/live', 'Live stream');
            }

            if ($type == 'file') {
                $mailSubject = 'Motion-UI - ' . $cameraName . ' - event #' . $motionEventIdShort . ' - Attached file';
                $mailMessage = '<p><b>Camera</b>: ' . $cameraName . '<br>';
                $mailMessage .= '<b>Event</b>: #' . $motionEventIdShort . ' (#' . $motionEventId . ')<br>';
                $mailMessage .= '<b>Date</b>: ' . $date . ' ' . $time . '<br><br></p>';

                /**
                 *  Calculate attached file size
                 */
                $fileSize = filesize($file);

                /**
                 *  If file size is lower than 10MB, then attach it to the mail
                 */
                if ($fileSize < 10000000) {
                    $mailMessage .= '<p>A new attached file has been generated from this event.<br></p>';
                    $mymail = new \Controllers\Mail($alertRecipient, $mailSubject, $mailMessage, 'http://' . WWW_HOSTNAME . '/live', 'Live stream', $file);
                } else {
                    $mailMessage .= '<p>Cannot attach file to the mail because it is too big (>10MB).<br></p>';
                    $mymail = new \Controllers\Mail($alertRecipient, $mailSubject, $mailMessage, 'http://' . WWW_HOSTNAME . '/live', 'Live stream');
                }
            }
        }
    }

    /**
     *  Get events details for the specified date, with offset
     *  It is possible to add an offset to the request
     */
    public function getByDate(string $date, bool $withOffset = false, int $offset = 0)
    {
        return $this->model->getByDate($date, $withOffset, $offset);
    }

    /**
     *  Return events between dates
     */
    public function getBetweenDate(string $dateStart, string $dateEnd)
    {
        return $this->model->getBetweenDate($dateStart, $dateEnd);
    }

    /**
     *  Get total recorded files count for the specified date
     */
    public function getTotalFileByDate(string $date)
    {
        return count($this->model->getFilesByDate($date));
    }

    /**
     *  Get files recorded for the specified motion event Id
     */
    public function getFilesByMotionEventId(string $motionEventId)
    {
        return $this->model->getFilesByMotionEventId($motionEventId);
    }

    /**
     *  Return total files count from an event
     */
    public function getTotalFilesByMotionEventId(string $motionEventId)
    {
        return count($this->model->getFilesByMotionEventId($motionEventId));
    }

    /**
     *  Return event short Id from full Id
     */
    public function getEventIdShort(string $motionEventId)
    {
        return $this->model->getEventIdShort($motionEventId);
    }

    /**
     *  Get event image or video path to visualize
     */
    public function getFilePath(string $fileId)
    {
        /**
         *  File Id must be numeric
         */
        if (!is_numeric($fileId)) {
            throw new Exception('The specified file is invalid.');
        }

        return $this->model->getFilePath($fileId);
    }

    /**
     *  Check if event exists
     */
    public function exists(string $motionEventId)
    {
        return $this->model->exists($motionEventId);
    }

    /**
     *  Delete event media file(s)
     */
    public function deleteFile(array $filesId)
    {
        // TODO: add a permission for usage users to delete or not files
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to delete files');
        }

        if (empty($filesId)) {
            throw new Exception('No file has been specified');
        }

        foreach ($filesId as $fileId) {
            /**
             *  Get file path
             */
            $filePath = $this->model->getFilePath($fileId);

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
     *  Clean events medias older than specified date (only clean files, not database entries)
     */
    public function clean(int $retention = 30)
    {
        $date = date('Y-m-d', strtotime('-' . $retention . ' days'));

        /**
         *  Get events with medias older than specified date
         */
        $events = $this->model->getDetailsBeforeDate($date);

        if (empty($events)) {
            return;
        }

        /**
         *  Delete files
         */
        foreach ($events as $event) {
            $file = $event['File'];

            if (empty($file)) {
                continue;
            }

            if (!file_exists($file)) {
                continue;
            }

            /**
             *  Delete file
             */
            if (!is_writeable($file)) {
                $this->logController->log('error', 'Event cleanup', 'Cannot delete file <b>' . $file . '</b> (file is not writeable).');
                continue;
            }

            if (!unlink($file)) {
                $this->logController->log('error', 'Event cleanup', 'Error while deleting file <b>' . $file . '</b>.');
                continue;
            }

            /**
             *  Delete file thumbnail if exists
             */
            if (file_exists($file . '.thumbnail.jpg')) {
                if (!is_writeable($file . '.thumbnail.jpg')) {
                    $this->logController->log('error', 'Event cleanup', 'Cannot delete file <b>' . $file . '</b> (file is not writeable).');
                    continue;
                }

                if (!unlink($file . '.thumbnail.jpg')) {
                    $this->logController->log('error', 'Event cleanup', 'Error while deleting file <b>' . $file . '.thumbnail.jpg</b>.');
                    continue;
                }
            }
        }
    }
}
