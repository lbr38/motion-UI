<?php

namespace Models\Motion;

use Exception;

class Event extends \Models\Model
{
    /**
     *  Get events details for the specified date, with offset
     *  It is possible to add an offset to the request
     */
    public function getByDate(string $date, bool $withOffset, int $offset)
    {
        $events = array();

        $query = "SELECT * FROM motion_events WHERE Date_start = :date ORDER BY Time_start DESC";

        /**
         *  If offset is specified
         */
        if ($withOffset) {
            $query .= " LIMIT 5 OFFSET :offset";
        }

        /**
         *  Prepare query
         */
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':date', $date);
        $stmt->bindValue(':offset', $offset);
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
    public function getFilesByMotionEventId(string $motionEventId)
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
        $stmt = $this->db->prepare("INSERT INTO motion_events (Motion_id_event, Motion_id_event_short, Date_start, Time_start, Camera_id, Status, Seen) VALUES (:id, :idShort, :dateStart, :timeStart, :cameraId, 'processing', 'false')");
        $stmt->bindValue(':id', $motionEventId);
        $stmt->bindValue(':idShort', $motionEventIdShort);
        $stmt->bindValue(':dateStart', $dateStart);
        $stmt->bindValue(':timeStart', $timeStart);
        $stmt->bindValue(':cameraId', $cameraId);
        $stmt->execute();
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
        $stmt->execute();
    }

    /**
     *  Acquit all events
     */
    public function acquitAll()
    {
        $stmt = $this->db->prepare("UPDATE motion_events SET Seen = 'true'");
        $stmt->execute();
    }

    /**
     *  Mark an event as seen
     */
    public function seen(int $id)
    {
        $stmt = $this->db->prepare("UPDATE motion_events SET Seen = 'true' WHERE Id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }

    /**
     *  Return total unseen events count
     */
    public function getUnseenCount()
    {
        $count = 0;

        $stmt = $this->db->prepare("SELECT COUNT(*) as Count FROM motion_events WHERE Seen = 'false'");
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $count = $row['Count'];
        }

        return $count;
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
        $stmt->execute();
    }
}
