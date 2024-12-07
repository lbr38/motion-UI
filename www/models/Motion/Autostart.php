<?php

namespace Models\Motion;

use Exception;

class Autostart extends \Models\Model
{
    public function __construct()
    {
        $this->getConnection('main');
    }

    /**
     *  Returns actual autostart time slots configuration
     */
    public function getConfiguration()
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
    public function getDevices()
    {
        $devices = array();

        $result = $this->db->query("SELECT * FROM devices");

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $devices[] = $row;
        }

        return $devices;
    }

    /**
     *  Enable / disable motion autostart
     */
    public function enable(string $status)
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
    public function configure(string $mondayStart, string $mondayEnd, string $tuesdayStart, string $tuesdayEnd, string $wednesdayStart, string $wednesdayEnd, string $thursdayStart, string $thursdayEnd, string $fridayStart, string $fridayEnd, string $saturdayStart, string $saturdayEnd, string $sundayStart, string $sundayEnd)
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
}
