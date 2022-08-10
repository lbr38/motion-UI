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
     *  Return a list of the last events' files
     */
    public function getLastEventsFiles()
    {
        $eventsFiles = array();

        $result = $this->db->query("SELECT
        motion_events_files.Id AS File_id,
        motion_events_files.File,
        motion_events_files.Id_event,
        motion_events.*
        FROM motion_events_files
        LEFT JOIN motion_events ON motion_events_files.Id_event = motion_events.Id 
        ORDER BY Date_start DESC, Time_start DESC");

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $eventsFiles[] = $row;
        }

        return $eventsFiles;
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
    public function configureAlert(string $mondayStart, string $mondayEnd, string $tuesdayStart, string $tuesdayEnd, string $wednesdayStart, string $wednesdayEnd, string $thursdayStart, string $thursdayEnd, string $fridayStart, string $fridayEnd, string $saturdayStart, string $saturdayEnd, string $sundayStart, string $sundayEnd, string $mailRecipient, string $muttConfig)
    {
        $stmt = $this->db->prepare("UPDATE alerts SET
        Recipient = :mailRecipient,
        Mutt_config = :muttConfig,
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
        $stmt->bindValue(':muttConfig', $muttConfig);
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
