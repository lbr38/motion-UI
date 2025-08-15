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
}
