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
    public function getMotionServiceStatusStats(int $start, int $end) : array
    {
        $data = [];

        try {
            $stmt = $this->db->prepare("SELECT * FROM motion_status WHERE Timestamp BETWEEN :start AND :end ORDER BY Timestamp ASC");
            $stmt->bindValue(':start', $start);
            $stmt->bindValue(':end', $end);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     *  Set motion actual status in database
     */
    public function setStatusInDb(string $status) : void
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO motion_status (Timestamp, Status) VALUES (:timestamp, :status)");
            $stmt->bindValue(':timestamp', time());
            $stmt->bindValue(':status', $status);
            $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }
    }
}
