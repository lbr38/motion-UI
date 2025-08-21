<?php

namespace Models\System;

use Exception;

class Memory extends \Models\Model
{
    public function __construct()
    {
        $this->getConnection('main');
    }

    /**
     *  Get memory usage for the last 60 minutes
     */
    public function get() : array
    {
        $data = [];

        try {
            // timestamp is in the time() format
            $stmt = $this->db->prepare("SELECT Timestamp, Memory_usage FROM system_monitoring WHERE Timestamp >= :timestampStart");
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
