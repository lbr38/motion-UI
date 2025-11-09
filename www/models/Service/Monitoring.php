<?php

namespace Models\Service;

use Exception;

class Monitoring extends \Models\Model
{
    public function __construct()
    {
        $this->getConnection('main');
    }

    /**
     *  Clean up old monitoring data
     */
    public function clean(string $timestamp) : void
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM system_monitoring WHERE Timestamp < :timestamp");
            $stmt->bindValue(':timestamp', $timestamp);
            $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }
    }
}
