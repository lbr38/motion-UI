<?php

namespace Models\Camera;

use Exception;

class Camera extends \Models\Model
{
    /**
     *  Get all cameras
     */
    public function get()
    {
        $cameras = array();

        $result = $this->db->query("SELECT * FROM cameras");

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $cameras[] = $row;
        }

        return $cameras;
    }

    /**
     *  Get camera Id by its name
     */
    public function getIdByName(string $name)
    {
        $id = '';

        $stmt = $this->db->prepare("SELECT Id FROM cameras WHERE Name = :name");
        $stmt->bindValue(':name', $name);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $id = $row['Id'];
        }

        return $id;
    }

    /**
     *  Get camera name by its Id
     */
    public function getNameById(string $id)
    {
        $name = '';

        $stmt = $this->db->prepare("SELECT Name FROM cameras WHERE Id = :id");
        $stmt->bindValue(':id', $id);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $name = $row['Name'];
        }

        return $name;
    }

    /**
     *  Get camera name by motion event Id
     */
    public function getNameByEventId(string $motionEventId)
    {
        $name = '';

        $stmt = $this->db->prepare("SELECT Name FROM cameras
        LEFT JOIN motion_events ON motion_events.Camera_id = cameras.Id
        WHERE motion_events.Motion_id_event = :motionEventId");
        $stmt->bindValue(':motionEventId', $motionEventId);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $name = $row['Name'];
        }

        return $name;
    }

    /**
     *  Returns all camera Id
     */
    public function getCamerasIds()
    {
        $id = array();

        $result = $this->db->query("SELECT Id FROM cameras");

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $id[] = $row['Id'];
        }

        return $id;
    }

    /**
     *  Get camera's configuration
     */
    public function getConfiguration(string $id)
    {
        $configuration = '';

        $stmt = $this->db->prepare("SELECT * FROM cameras WHERE Id = :id");
        $stmt->bindValue(':id', $id);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $configuration = $row;
        }

        return $configuration;
    }

    /**
     *  Add a new camera
     */
    public function add(string $name, string $url, string $resolution, int $framerate, string $basicAuthUsername, string $basicAuthPassword, string $motionEnabled)
    {
        $stmt = $this->db->prepare("INSERT INTO cameras ('Name', 'Url', 'Output_resolution', 'Framerate', 'Username', 'Password', 'Live_enabled', 'Motion_enabled', 'Hardware_acceleration') VALUES (:name, :url, :resolution, :framerate, :username, :password, 'true', :motionEnabled, 'false')");
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':url', $url);
        $stmt->bindValue(':resolution', $resolution);
        $stmt->bindValue(':framerate', $framerate);
        $stmt->bindValue(':username', $basicAuthUsername);
        $stmt->bindValue(':password', $basicAuthPassword);
        $stmt->bindValue(':motionEnabled', $motionEnabled);
        $stmt->execute();
    }

    /**
     *  Edit camera global settings
     */
    public function editGlobalSettings(int $id, string $name, string $url, string $resolution, int $framerate, string $rotate, string $textLeft, string $textRight, string $basicAuthUsername, string $basicAuthPassword, string $liveEnabled, string $timestampLeft, string $timestampRight, string $motionEnabled, string $timelapseEnabled, string $hardwareAcceleration)
    {
        $stmt = $this->db->prepare("UPDATE cameras SET Name = :name, Url = :url, Output_resolution = :outputResolution, Framerate = :framerate, Rotate = :rotate, Text_left = :textLeft, Text_right = :textRight, Username = :username, Password = :password, Live_enabled = :liveEnabled, Timestamp_left = :timestampLeft, Timestamp_right = :timestampRight, Motion_enabled = :motionEnabled, 'Timelapse_enabled' = :timelapseEnabled, 'Hardware_acceleration' = :hardwareAcceleration WHERE Id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':url', $url);
        $stmt->bindValue(':outputResolution', $resolution);
        $stmt->bindValue(':framerate', $framerate);
        $stmt->bindValue(':rotate', $rotate);
        $stmt->bindValue(':textLeft', $textLeft);
        $stmt->bindValue(':textRight', $textRight);
        $stmt->bindValue(':username', $basicAuthUsername);
        $stmt->bindValue(':password', $basicAuthPassword);
        $stmt->bindValue(':liveEnabled', $liveEnabled);
        $stmt->bindValue(':timestampLeft', $timestampLeft);
        $stmt->bindValue(':timestampRight', $timestampRight);
        $stmt->bindValue(':motionEnabled', $motionEnabled);
        $stmt->bindValue(':timelapseEnabled', $timelapseEnabled);
        $stmt->bindValue(':hardwareAcceleration', $hardwareAcceleration);
        $stmt->execute();
    }

    /**
     *  Delete camera
     */
    public function delete(string $id)
    {
        $stmt = $this->db->prepare("DELETE FROM cameras WHERE Id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }

    /**
     *  Check if camera Id exist
     */
    public function existId(string $id)
    {
        $stmt = $this->db->prepare("SELECT Id FROM cameras WHERE Id = :id");
        $stmt->bindValue(':id', $id);
        $result = $stmt->execute();

        if ($this->db->isempty($result)) {
            return false;
        }

        return true;
    }
}
