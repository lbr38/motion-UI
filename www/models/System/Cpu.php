<?php

namespace Models\System;

use Exception;

class Cpu extends \Models\Model
{
    public function __construct()
    {
        $this->getConnection('main');
    }

    /**
     *  Get CPU usage for the last 60 minutes
     */
    public function get() : array
    {
        $data = [];

        try {
            $stmt = $this->db->prepare("SELECT Timestamp, Cpu_usage FROM system_monitoring WHERE Timestamp >= :timestampStart");
            $stmt->bindValue(':timestampStart', time() - 3600);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }
}
