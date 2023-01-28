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
    public function edit(string $streamMainPage, string $streamLivePage, string $motionStartBtn, string $motionAutostartBtn, string $motionAlertBtn, string $motionEvents, string $motionEventsVideosThumbnail, string $motionEventsPicturesThumbnail, string $motionStats)
    {
        $stmt = $this->db->prepare("UPDATE Settings SET 
        Stream_on_main_page = :streamMainPage,
        Stream_on_live_page = :streamLivePage,
        Motion_start_btn = :motionStartBtn,
        Motion_autostart_btn = :motionAutostartBtn,
        Motion_alert_btn = :motionAlertBtn,
        Motion_events = :motionEvents,
        Motion_events_videos_thumbnail = :motionEventsVideosThumbnail,
        Motion_events_pictures_thumbnail = :motionEventsPicturesThumbnail,
        Motion_stats = :motionStats");

        $stmt->bindValue(':streamMainPage', $streamMainPage);
        $stmt->bindValue(':streamLivePage', $streamLivePage);
        $stmt->bindValue(':motionStartBtn', $motionStartBtn);
        $stmt->bindValue(':motionAutostartBtn', $motionAutostartBtn);
        $stmt->bindValue(':motionAlertBtn', $motionAlertBtn);
        $stmt->bindValue(':motionEvents', $motionEvents);
        $stmt->bindValue(':motionEventsVideosThumbnail', $motionEventsVideosThumbnail);
        $stmt->bindValue(':motionEventsPicturesThumbnail', $motionEventsPicturesThumbnail);
        $stmt->bindValue(':motionStats', $motionStats);
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
