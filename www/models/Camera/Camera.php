<?php

namespace Models\Camera;

use Exception;

class Camera extends \Models\Model
{
    public function __construct()
    {
        $this->getConnection('main');
    }

    /**
     *  Get all cameras
     */
    public function get() : array
    {
        $cameras = array();

        try {
            $result = $this->db->query("SELECT * FROM cameras");
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $cameras[] = $row;
        }

        return $cameras;
    }

    /**
     *  Get camera name by its Id
     */
    public function getNameById(string $id) : string
    {
        $name = '';

        try {
            $stmt = $this->db->prepare("SELECT json_extract(COALESCE(Configuration, '{}'), '$.name') as Name FROM cameras WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $name = $row['Name'];
        }

        return $name;
    }

    /**
     *  Get camera name by motion event Id
     */
    public function getNameByEventId(string $motionEventId) : string
    {
        $name = '';

        try {
            $stmt = $this->db->prepare("SELECT json_extract(COALESCE(Configuration, '{}'), '$.name') as Name FROM cameras
            LEFT JOIN motion_events ON motion_events.Camera_id = cameras.Id
            WHERE motion_events.Motion_id_event = :motionEventId");
            $stmt->bindValue(':motionEventId', $motionEventId);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $name = $row['Name'];
        }

        return $name;
    }

    /**
     *  Returns all camera Id
     */
    public function getCamerasIds() : array
    {
        $id = array();

        try {
            $result = $this->db->query("SELECT Id FROM cameras");
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $id[] = $row['Id'];
        }


        return $id;
    }

    /**
     *  Get camera's configuration
     */
    public function getConfiguration(string $id) : array
    {
        $configuration = array();

        try {
            $stmt = $this->db->prepare("SELECT * FROM cameras WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $configuration = $row;
        }

        return $configuration;
    }

    /**
     *  Add a new camera
     */
    public function add() : void
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO cameras ('Configuration') VALUES ('')");
            $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }
    }

    /**
     *  Save camera's global configuration
     */
    public function saveGlobalConfiguration(string $id, string $configuration) : void
    {
        try {
            $stmt = $this->db->prepare("UPDATE cameras SET Configuration = :configuration WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':configuration', $configuration);
            $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }
    }

    /**
     *  Save camera's motion configuration
     */
    public function saveMotionConfiguration(string $id, string $configuration) : void
    {
        try {
            $stmt = $this->db->prepare("UPDATE cameras SET Motion_configuration = :configuration WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':configuration', $configuration);
            $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }
    }

    /**
     *  Delete camera
     */
    public function delete(string $id) : void
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM cameras WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }
    }

    /**
     *  Check if camera Id exist
     */
    public function existId(string $id) : bool
    {
        try {
            $stmt = $this->db->prepare("SELECT Id FROM cameras WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $result = $stmt->execute();

            if ($this->db->isempty($result)) {
                return false;
            }
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        return true;
    }
}
