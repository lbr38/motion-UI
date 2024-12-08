<?php

namespace Models\Motion;

use Exception;

class Service extends \Models\Model
{
    public function __construct()
    {
        $this->getConnection('main');
    }

    /**
     *  Get daily motion service status (for stats)
     */
    public function getMotionServiceStatusStats()
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
     *  Set motion actual status in database
     */
    public function setStatusInDb(string $status)
    {
        $stmt = $this->db->prepare("INSERT INTO motion_status (Date, Time, Status) VALUES (:date, :time, :status)");
        $stmt->bindValue(':date', date('Y-m-d'));
        $stmt->bindValue(':time', date('H:i:s'));
        $stmt->bindValue(':status', $status);
        $stmt->execute();
    }
}
