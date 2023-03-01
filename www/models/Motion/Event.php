<?php

namespace Models\Motion;

use Exception;

class Event extends \Models\Model
{
    /**
     *  Get event for the specified date
     */
    public function getByDate(string $date)
    {
        $events = array();

        $stmt = $this->db->prepare("SELECT * FROM motion_events WHERE Date_start = :date");
        $stmt->bindValue(':date', $date);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $events[] = $row;
        }

        return $events;
    }

    /**
     *  Return events between dates
     */
    public function getBetweenDate(string $dateStart, string $dateEnd)
    {
        $events = array();

        $stmt = $this->db->prepare("SELECT DISTINCT Date_start
        FROM motion_events
        WHERE Date_start BETWEEN :dateStart AND :dateEnd 
        ORDER BY Date_start DESC");
        $stmt->bindValue(':dateStart', $dateStart);
        $stmt->bindValue(':dateEnd', $dateEnd);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $events[] = $row;
        }

        return $events;
    }

    /**
     *  Return events time by date
     */
    public function getTimeByDate(string $date)
    {
        $events = array();

        $stmt = $this->db->prepare("SELECT DISTINCT Time_start, Status
        FROM motion_events
        WHERE Date_start = :date
        ORDER BY Time_start DESC");
        $stmt->bindValue(':date', $date);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $events[] = $row;
        }

        return $events;
    }

    /**
     *  Return all events details by date and time
     */
    public function getDetailsByDate(string $date, string $time)
    {
        $events = array();

        $stmt = $this->db->prepare("SELECT motion_events.*,
        motion_events_files.Id as FileId,
        motion_events_files.Size,
        motion_events_files.File,
        motion_events_files.Width,
        motion_events_files.Height,
        motion_events_files.Fps,
        motion_events_files.Changed_pixels
        FROM motion_events
        LEFT JOIN motion_events_files
        ON motion_events.Motion_id_event = motion_events_files.Motion_id_event
        WHERE Date_start = :date
        AND Time_start = :time");
        $stmt->bindValue(':date', $date);
        $stmt->bindValue(':time', $time);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $events[] = $row;
        }

        return $events;
    }

    /**
     *  Return all events details before the specified date
     */
    public function getDetailsBeforeDate(string $date)
    {
        $events = array();

        $stmt = $this->db->prepare("SELECT motion_events.*,
        motion_events_files.Id as FileId,
        motion_events_files.Size,
        motion_events_files.File
        FROM motion_events
        LEFT JOIN motion_events_files
        ON motion_events.Motion_id_event = motion_events_files.Motion_id_event
        WHERE Date_start < :date");
        $stmt->bindValue(':date', $date);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $events[] = $row;
        }

        return $events;
    }

    /**
     *  Return event short Id from full Id
     */
    public function getEventIdShort(string $motionEventId)
    {
        $eventIdShort = '';

        $stmt = $this->db->prepare("SELECT Motion_id_event_short FROM motion_events WHERE Motion_id_event = :id");
        $stmt->bindValue(':id', $motionEventId);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $eventIdShort = $row['Motion_id_event_short'];
        }

        return $eventIdShort;
    }

    /**
     *  Get files recorded for the specified motion event Id
     */
    public function getFilesById(string $motionEventId)
    {
        $files = array();

        $stmt = $this->db->prepare("SELECT *
        FROM motion_events_files
        WHERE Motion_id_event = :eventId");
        $stmt->bindValue(':eventId', $motionEventId);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $files[] = $row;
        }

        return $files;
    }

    /**
     *  Get files recorded for the specified date
     */
    public function getFilesByDate(string $date)
    {
        $files = array();

        $stmt = $this->db->prepare("SELECT *
        FROM motion_events_files
        LEFT JOIN motion_events
        ON motion_events.Motion_id_event = motion_events_files.Motion_id_event
        WHERE Date_start = :date");
        $stmt->bindValue(':date', $date);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $files[] = $row;
        }

        return $files;
    }

    /**
     *  Get path of a file from its Id
     */
    public function getFilePath($fileId)
    {
        $filePath = '';

        $stmt = $this->db->prepare("SELECT File FROM motion_events_files WHERE Id = :fileId");
        $stmt->bindValue(':fileId', $fileId);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $filePath = $row['File'];
        }

        return $filePath;
    }

    /**
     *  Check if event exists
     */
    public function exists(string $motionEventId)
    {
        $stmt = $this->db->prepare("SELECT Id FROM motion_events WHERE Motion_id_event = :id");
        $stmt->bindValue(':id', $motionEventId);
        $result = $stmt->execute();

        if ($this->db->isempty($result) === true) {
            return false;
        }

        return true;
    }

    /**
     *  Add a new event in database
     */
    public function new(string $motionEventId, int $motionEventIdShort, string $dateStart, string $timeStart, int $cameraId)
    {
        $stmt = $this->db->prepare("INSERT INTO motion_events (Motion_id_event, Motion_id_event_short, Date_start, Time_start, Camera_id, Status) VALUES (:id, :idShort, :dateStart, :timeStart, :cameraId, 'processing')");
        $stmt->bindValue(':id', $motionEventId);
        $stmt->bindValue(':idShort', $motionEventIdShort);
        $stmt->bindValue(':dateStart', $dateStart);
        $stmt->bindValue(':timeStart', $timeStart);
        $stmt->bindValue(':cameraId', $cameraId);
        $result = $stmt->execute();
    }

    /**
     *  End an event
     */
    public function end(string $motionEventId)
    {
        $stmt = $this->db->prepare("UPDATE motion_events SET Status = 'done', Date_end = :dateEnd, Time_end = :timeEnd WHERE Motion_id_event = :id AND Status = 'processing'");
        $stmt->bindValue(':id', $motionEventId);
        $stmt->bindValue(':dateEnd', date('Y-m-d'));
        $stmt->bindValue(':timeEnd', date('H:i:s'));
        $result = $stmt->execute();
    }

    /**
     *  Attach a file to an event
     */
    public function attachFile(string $motionEventId, string $file, string $fileSize, int $width, int $height, int $fps, int $changed_pixels)
    {
        $stmt = $this->db->prepare("INSERT INTO motion_events_files (File, Size, Width, Height, Fps, Changed_pixels, Motion_id_event) VALUES (:file, :fileSize, :width, :height, :fps, :changed_pixels, :id)");
        $stmt->bindValue(':file', $file);
        $stmt->bindValue(':fileSize', $fileSize);
        $stmt->bindValue(':width', $width);
        $stmt->bindValue(':height', $height);
        $stmt->bindValue(':fps', $fps);
        $stmt->bindValue(':changed_pixels', $changed_pixels);
        $stmt->bindValue(':id', $motionEventId);
        $result = $stmt->execute();
    }
}
