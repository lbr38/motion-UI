<?php

namespace Models\Camera;

use Exception;

class Stream extends \Models\Model
{
    public function __construct()
    {
        $this->getConnection('main');
    }

    /**
     *  Get the camera grid order
     */
    public function getOrder() : string
    {
        $data = '';

        try {
            $result = $this->db->query("SELECT `Order` FROM cameras_sort");
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data = $row['Order'];
        }

        return $data;
    }

    /**
     *  Sort the camera grid
     */
    public function sort(string $order) : void
    {
        // Empty the current order before inserting the new one
        try {
            $this->db->exec('BEGIN');

            // Delete current order
            $this->db->query("DELETE FROM cameras_sort");

            // Insert new order
            $stmt = $this->db->prepare("INSERT INTO cameras_sort ('Order') VALUES (:order)");
            $stmt->bindValue(':order', $order);
            $stmt->execute();

            $this->db->exec('COMMIT');
        } catch (Exception $e) {
            $this->db->exec('ROLLBACK');
            $this->db->logError($e->getMessage());
        }
    }

    /**
     *  Get latest camera stream status from the database
     */
    public function getLatestStatus(int $id) : array
    {
        $data = [];

        try {
            $stmt = $this->db->prepare("SELECT * FROM camera_monitoring WHERE Camera_id = :camera_id ORDER BY Timestamp DESC LIMIT 1");
            $stmt->bindValue(':camera_id', $id);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data = $row;
        }

        return $data;
    }

    /**
     *  Set camera stream status in the database
     */
    public function setStatus(int $id, int $mainStreamStatus, int $secStreamStatus, string $mainStreamDetails = '', string $secStreamDetails = '') : void
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO camera_monitoring (Timestamp, Main_stream_status, Secondary_stream_status, Main_stream_error, Secondary_stream_error, Camera_id) VALUES (:timestamp, :main_stream_status, :secondary_stream_status, :Main_stream_error, :Secondary_stream_error, :camera_id)");
            $stmt->bindValue(':timestamp', time());
            $stmt->bindValue(':main_stream_status', $mainStreamStatus);
            $stmt->bindValue(':secondary_stream_status', $secStreamStatus);
            $stmt->bindValue(':Main_stream_error', $mainStreamDetails);
            $stmt->bindValue(':Secondary_stream_error', $secStreamDetails);
            $stmt->bindValue(':camera_id', $id);
            $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }
    }
}
