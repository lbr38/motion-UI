<?php

namespace Models;

use Exception;

class Settings extends Model
{
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
    public function edit(string $motionEventsVideosThumbnail, string $motionEventsPicturesThumbnail, int $motionEventsRetention)
    {
        $stmt = $this->db->prepare("UPDATE Settings SET 
        Motion_events_videos_thumbnail = :motionEventsVideosThumbnail,
        Motion_events_pictures_thumbnail = :motionEventsPicturesThumbnail,
        Motion_events_retention = :motionEventsRetention");

        $stmt->bindValue(':motionEventsVideosThumbnail', $motionEventsVideosThumbnail);
        $stmt->bindValue(':motionEventsPicturesThumbnail', $motionEventsPicturesThumbnail);
        $stmt->bindValue(':motionEventsRetention', $motionEventsRetention);
        $stmt->execute();
    }

    /**
     *  Enable / disable motion configuration's advanced edition mode
     */
    public function motionAdvancedEditionMode(string $status)
    {
        $stmt = $this->db->prepare("UPDATE Settings SET Motion_advanced_edition_mode = :status");
        $stmt->bindValue(':status', $status);
        $stmt->execute();
    }
}
