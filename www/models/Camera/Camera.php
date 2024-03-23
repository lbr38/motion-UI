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
    public function add(string $name, string $url, string $streamUrl, string $outputType, string $outputResolution, string $liveEnable, string $motionEnable, string $timelapseEnable, string $username, string $password)
    {
        $stmt = $this->db->prepare("INSERT INTO cameras ('Name', 'Url', 'Stream_url', 'Output_type', 'Output_resolution', 'Text_left', 'Text_right', 'Timestamp_left', 'Timestamp_right', 'Refresh', 'Live_enabled', 'Motion_enabled', 'Timelapse_enabled', 'Username', 'Password') VALUES (:name, :url, :streamUrl, :outputType, :outputResolution, :textLeft, :textRight, 'false', 'false', '3', :liveEnable, :motionEnable, :timelapseEnable, :username, :password)");
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':url', $url);
        $stmt->bindValue(':streamUrl', $streamUrl);
        $stmt->bindValue(':outputType', $outputType);
        $stmt->bindValue(':outputResolution', $outputResolution);
        $stmt->bindValue(':liveEnable', $liveEnable);
        $stmt->bindValue(':motionEnable', $motionEnable);
        $stmt->bindValue(':timelapseEnable', $timelapseEnable);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', $password);
        $stmt->execute();
    }

    /**
     *  Edit camera global settings
     */
    public function editGlobalSettings(string $id, string $name, string $url, string $streamUrl, string $outputResolution, string $rotate, string $textLeft, string $textRight, string $liveEnable, string $motionEnable, string $timelapseEnable, string $username, string $password)
    {
        $stmt = $this->db->prepare("UPDATE cameras SET Name = :name, Url = :url, Stream_url = :streamUrl, Output_resolution = :outputResolution, Rotate = :rotate, Text_left = :textLeft, Text_right = :textRight, Live_enabled = :liveEnable, Motion_enabled = :motionEnable, Timelapse_enabled = :timelapseEnable, Username = :username, Password = :password WHERE Id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':url', $url);
        $stmt->bindValue(':streamUrl', $streamUrl);
        $stmt->bindValue(':outputResolution', $outputResolution);
        $stmt->bindValue(':rotate', $rotate);
        $stmt->bindValue(':textLeft', $textLeft);
        $stmt->bindValue(':textRight', $textRight);
        $stmt->bindValue(':liveEnable', $liveEnable);
        $stmt->bindValue(':motionEnable', $motionEnable);
        $stmt->bindValue(':timelapseEnable', $timelapseEnable);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', $password);
        $stmt->execute();
    }

    /**
     *  Edit camera stream settings
     */
    public function editStreamSettings(string $id, int $refresh, string $timestampLeft, string $timestampRight)
    {
        $stmt = $this->db->prepare("UPDATE cameras SET Refresh = :refresh, Timestamp_left = :timestampLeft, Timestamp_right = :timestampRight WHERE Id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':refresh', $refresh);
        $stmt->bindValue(':timestampLeft', $timestampLeft);
        $stmt->bindValue(':timestampRight', $timestampRight);
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
