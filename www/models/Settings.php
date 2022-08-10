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
    public function edit(string $printLiveBtn, string $printMotionStartBtn, string $printMotionAutostartBtn, string $printMotionAlertBtn, string $printMotionStatsBtn, string $printMotionsCaptures, string $printMotionConfig)
    {
        $stmt = $this->db->prepare("UPDATE Settings SET 
        Print_live_btn = :printLiveBtn,
        Print_motion_start_btn = :printMotionStartBtn,
        Print_motion_autostart_btn = :printMotionAutostartBtn,
        Print_motion_alert_btn = :printMotionAlertBtn,
        Print_motion_stats = :printMotionStatsBtn,
        Print_motion_captures = :printMotionsCaptures,
        Print_motion_config = :printMotionConfig");
        $stmt->bindValue(':printLiveBtn', $printLiveBtn);
        $stmt->bindValue(':printMotionStartBtn', $printMotionStartBtn);
        $stmt->bindValue(':printMotionAutostartBtn', $printMotionAutostartBtn);
        $stmt->bindValue(':printMotionAlertBtn', $printMotionAlertBtn);
        $stmt->bindValue(':printMotionStatsBtn', $printMotionStatsBtn);
        $stmt->bindValue(':printMotionsCaptures', $printMotionsCaptures);
        $stmt->bindValue(':printMotionConfig', $printMotionConfig);
        $stmt->execute();
    }
}
