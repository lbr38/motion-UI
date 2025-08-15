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
     *  Save CPU and memory usage data to the database
     */
    public function save(string $timestamp, float $cpuUsage, float $memoryUsage) : void
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO system_monitoring (Timestamp, Cpu_usage, Memory_usage) VALUES (:timestamp, :cpuUsage, :memoryUsage)");
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->bindParam(':cpuUsage', $cpuUsage);
            $stmt->bindParam(':memoryUsage', $memoryUsage);
            $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }
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
