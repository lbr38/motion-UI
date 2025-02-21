<?php

namespace Models;

use Exception;

class Settings extends Model
{
    public function __construct()
    {
        $this->getConnection('main');
    }

    /**
     *  Return global settings
     */
    public function get()
    {
        $settings = array();

        $result = $this->db->query("SELECT * FROM settings");

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $settings = $row;
        }

        return $settings;
    }

    /**
     *  Edit global settings
     */
    public function edit(string $streamDefaultTechnology, string $timelapseInterval, string $timelapseRetention, int $motionEventsRetention)
    {
        $stmt = $this->db->prepare("UPDATE Settings SET
        Stream_default_technology = :streamDefaultTechnology,
        Timelapse_interval = :timelapseInterval,
        Timelapse_retention = :timelapseRetention,
        Motion_events_retention = :motionEventsRetention");

        $stmt->bindValue(':streamDefaultTechnology', $streamDefaultTechnology);
        $stmt->bindValue(':timelapseInterval', $timelapseInterval);
        $stmt->bindValue(':timelapseRetention', $timelapseRetention);
        $stmt->bindValue(':motionEventsRetention', $motionEventsRetention);
        $stmt->execute();
    }
}
