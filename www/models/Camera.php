<?php

namespace Models;

use Exception;

class Camera extends Model
{
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
    public function add(string $name, string $url, string $streamUrl, string $outputType, string $outputResolution, string $refresh, string $liveEnable, string $motionEnable, string $username, string $password)
    {
        $stmt = $this->db->prepare("INSERT INTO cameras ('Name', 'Url', 'Stream_url', 'Output_type', 'Output_resolution', 'Refresh', 'Live_enabled', 'Motion_enabled', 'Username', 'Password') VALUES (:name, :url, :streamUrl, :outputType, :outputResolution, :refresh, :liveEnable, :motionEnable, :username, :password)");
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':url', $url);
        $stmt->bindValue(':streamUrl', $streamUrl);
        $stmt->bindValue(':outputType', $outputType);
        $stmt->bindValue(':outputResolution', $outputResolution);
        $stmt->bindValue(':refresh', $refresh);
        $stmt->bindValue(':liveEnable', $liveEnable);
        $stmt->bindValue(':motionEnable', $motionEnable);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', $password);
        $stmt->execute();
    }

    /**
     *  Edit camera global settings
     */
    public function edit(string $id, string $name, string $url, string $streamUrl, string $outputResolution, string $refresh, string $rotate, string $liveEnable, string $motionEnable, string $username, string $password)
    {
        $stmt = $this->db->prepare("UPDATE cameras SET Name = :name, Url = :url, Stream_url = :streamUrl, Output_resolution = :outputResolution, Refresh = :refresh, Rotate = :rotate, Live_enabled = :liveEnable, Motion_enabled = :motionEnable, Username = :username, Password = :password WHERE Id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':url', $url);
        $stmt->bindValue(':streamUrl', $streamUrl);
        $stmt->bindValue(':outputResolution', $outputResolution);
        $stmt->bindValue(':refresh', $refresh);
        $stmt->bindValue(':rotate', $rotate);
        $stmt->bindValue(':liveEnable', $liveEnable);
        $stmt->bindValue(':motionEnable', $motionEnable);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', $password);
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
