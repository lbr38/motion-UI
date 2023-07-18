<?php

namespace Models\Motion;

use Exception;

class Device extends \Models\Model
{
    /**
     *  Add a new device name and ip address to known devices
     */
    public function add(string $name, string $ip)
    {
        $stmt = $this->db->prepare("INSERT INTO devices ('Name', 'Ip') VALUES (:name, :ip)");
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':ip', $ip);
        $stmt->execute();
    }

    /**
     *  Remove a known device
     */
    public function remove(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM devices WHERE Id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }
}
