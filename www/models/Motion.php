<?php

namespace Models;

use Exception;

class Motion extends Model
{
    /**
     *  Returns actual autostart time slots configuration
     */
    public function getAutostartConfiguration()
    {
        $config = array();

        $result = $this->db->query("SELECT * FROM autostart");

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $config = $row;
        }

        return $config;
    }

    /**
     *  Returns known devices
     */
    public function getAutostartDevices()
    {
        $devices = array();

        $result = $this->db->query("SELECT * FROM devices");

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $devices[] = $row;
        }

        return $devices;
    }

    /**
     *  Returns actual alerts time slots configuration
     */
    public function getAlertConfiguration()
    {
        $config = array();

        $result = $this->db->query("SELECT * FROM alerts");

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $config = $row;
        }

        return $config;
    }

    /**
     *  Get event for the specified date
     */
    public function getDailyEvent(string $date)
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
     *  Get files recorded for the specified date
     */
    public function getDailyFile(string $date)
    {
        $files = array();

        $stmt = $this->db->prepare("SELECT *
        FROM motion_events_files
        INNER JOIN motion_events
        ON motion_events.Id = motion_events_files.Id_event
        WHERE Date_start = :date");
        $stmt->bindValue(':date', $date);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $files[] = $row;
        }

        return $files;
    }

    /**
     *  Get total files recorded for the specified event Id
     *  Id must be the autoincrement Id in database, not the motion's event Id
     */
    public function getEventFile(string $eventId)
    {
        $files = array();

        $stmt = $this->db->prepare("SELECT *
        FROM motion_events_files
        WHERE Id_event = :eventId");
        $stmt->bindValue(':eventId', $eventId);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $files[] = $row;
        }

        return $files;
    }

    /**
     *  Return events between dates
     */
    public function getEventsDate(string $dateStart, string $dateEnd)
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
    public function getEventsTime(string $date)
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
    public function getEventsDetails(string $date, string $time)
    {
        $events = array();

        $stmt = $this->db->prepare("SELECT motion_events.*,
        motion_events_files.Id as FileId,
        motion_events_files.File,
        motion_events_files.Id_event
        FROM motion_events
        INNER JOIN motion_events_files
        ON motion_events.Id = motion_events_files.Id_event
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
     *  Return total event count by date
     */
    public function totalEventByDate(string $date)
    {
        $events = array();

        $stmt = $this->db->prepare("SELECT motion_events.Id
        FROM motion_events
        WHERE Date_start = :date");
        $stmt->bindValue(':date', $date);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $events[] = $row;
        }

        return $events;
    }

    /**
     *  Return total files count from an event
     */
    public function totalFilesByEventId(string $eventId)
    {
        $files = array();

        $stmt = $this->db->prepare("SELECT Id
        FROM motion_events_files
        WHERE Id_event = :id");
        $stmt->bindValue(':id', $eventId);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $files[] = $row;
        }

        return $files;
    }

    /**
     *  Get path of a file from its Id
     */
    public function getEventFilePath($fileId)
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
     *  Get daily motion service status (for stats)
     */
    public function getMotionServiceStatus()
    {
        $status = array();

        $stmt = $this->db->prepare("SELECT * FROM motion_status WHERE (Date = :dateYesterday AND Time >= :timeNow) OR (Date = :dateToday)");
        $stmt->bindValue(':dateYesterday', date('Y-m-d', strtotime('-1 day', strtotime(DATE_YMD))));
        $stmt->bindValue(':timeNow', date('H:i:s'));
        $stmt->bindValue(':dateToday', DATE_YMD);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $status[] = $row;
        }

        return $status;
    }

    /**
     *  Enable / disable motion autostart
     */
    public function enableAutostart(string $status)
    {
        $stmt = $this->db->prepare("UPDATE autostart SET Status = :status");
        $stmt->bindValue(':status', $status);
        $stmt->execute();
    }

    /**
     *  Enable / disable autostart on device presence
     */
    public function enableDevicePresence(string $status)
    {
        $stmt = $this->db->prepare("UPDATE autostart SET Device_presence = :status");
        $stmt->bindValue(':status', $status);
        $stmt->execute();
    }

    /**
     *  Configure motion autostart
     */
    public function configureAutostart(string $mondayStart, string $mondayEnd, string $tuesdayStart, string $tuesdayEnd, string $wednesdayStart, string $wednesdayEnd, string $thursdayStart, string $thursdayEnd, string $fridayStart, string $fridayEnd, string $saturdayStart, string $saturdayEnd, string $sundayStart, string $sundayEnd)
    {
        $stmt = $this->db->prepare("UPDATE autostart SET
        Monday_start = :mondayStart,
        Monday_end = :mondayEnd,
        Tuesday_start = :tuesdayStart,
        Tuesday_end = :tuesdayEnd,
        Wednesday_start = :wednesdayStart,
        Wednesday_end = :wednesdayEnd,
        Thursday_start = :thursdayStart,
        Thursday_end = :thursdayEnd,
        Friday_start = :fridayStart,
        Friday_end = :fridayEnd,
        Saturday_start = :saturdayStart,
        Saturday_end = :saturdayEnd,
        Sunday_start = :sundayStart,
        Sunday_end = :sundayEnd");
        $stmt->bindValue(':mondayStart', $mondayStart);
        $stmt->bindValue(':mondayEnd', $mondayEnd);
        $stmt->bindValue(':tuesdayStart', $tuesdayStart);
        $stmt->bindValue(':tuesdayEnd', $tuesdayEnd);
        $stmt->bindValue(':wednesdayStart', $wednesdayStart);
        $stmt->bindValue(':wednesdayEnd', $wednesdayEnd);
        $stmt->bindValue(':thursdayStart', $thursdayStart);
        $stmt->bindValue(':thursdayEnd', $thursdayEnd);
        $stmt->bindValue(':fridayStart', $fridayStart);
        $stmt->bindValue(':fridayEnd', $fridayEnd);
        $stmt->bindValue(':saturdayStart', $saturdayStart);
        $stmt->bindValue(':saturdayEnd', $saturdayEnd);
        $stmt->bindValue(':sundayStart', $sundayStart);
        $stmt->bindValue(':sundayEnd', $sundayEnd);
        $stmt->execute();
    }

    /**
     *  Add a new device name and ip address to known devices
     */
    public function addDevice(string $name, string $ip)
    {
        $stmt = $this->db->prepare("INSERT INTO devices ('Name', 'Ip') VALUES (:name, :ip)");
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':ip', $ip);
        $stmt->execute();
    }

    /**
     *  Remove a known device
     */
    public function removeDevice(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM devices WHERE Id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }

    /**
     *  Enable / disable motion alerts
     */
    public function enableAlert(string $status)
    {
        $stmt = $this->db->prepare("UPDATE alerts SET Status = :status");
        $stmt->bindValue(':status', $status);
        $stmt->execute();
    }

    /**
     *  Configure motion alerts
     */
    public function configureAlert(string $mondayStart, string $mondayEnd, string $tuesdayStart, string $tuesdayEnd, string $wednesdayStart, string $wednesdayEnd, string $thursdayStart, string $thursdayEnd, string $fridayStart, string $fridayEnd, string $saturdayStart, string $saturdayEnd, string $sundayStart, string $sundayEnd, string $mailRecipient)
    {
        $stmt = $this->db->prepare("UPDATE alerts SET
        Recipient = :mailRecipient,
        Monday_start = :mondayStart,
        Monday_end = :mondayEnd,
        Tuesday_start = :tuesdayStart,
        Tuesday_end = :tuesdayEnd,
        Wednesday_start = :wednesdayStart,
        Wednesday_end = :wednesdayEnd,
        Thursday_start = :thursdayStart,
        Thursday_end = :thursdayEnd,
        Friday_start = :fridayStart,
        Friday_end = :fridayEnd,
        Saturday_start = :saturdayStart,
        Saturday_end = :saturdayEnd,
        Sunday_start = :sundayStart,
        Sunday_end = :sundayEnd");
        $stmt->bindValue(':mailRecipient', $mailRecipient);
        $stmt->bindValue(':mondayStart', $mondayStart);
        $stmt->bindValue(':mondayEnd', $mondayEnd);
        $stmt->bindValue(':tuesdayStart', $tuesdayStart);
        $stmt->bindValue(':tuesdayEnd', $tuesdayEnd);
        $stmt->bindValue(':wednesdayStart', $wednesdayStart);
        $stmt->bindValue(':wednesdayEnd', $wednesdayEnd);
        $stmt->bindValue(':thursdayStart', $thursdayStart);
        $stmt->bindValue(':thursdayEnd', $thursdayEnd);
        $stmt->bindValue(':fridayStart', $fridayStart);
        $stmt->bindValue(':fridayEnd', $fridayEnd);
        $stmt->bindValue(':saturdayStart', $saturdayStart);
        $stmt->bindValue(':saturdayEnd', $saturdayEnd);
        $stmt->bindValue(':sundayStart', $sundayStart);
        $stmt->bindValue(':sundayEnd', $sundayEnd);
        $stmt->execute();
    }
}
