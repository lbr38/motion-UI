<?php
/**
 *  4.0.1 database update
 */

/**
 *  Add new index
 */
$this->db->exec("CREATE INDEX IF NOT EXISTS motion_events_files_motion_id_event_index ON motion_events_files (Motion_id_event)");
$this->db->exec("CREATE INDEX IF NOT EXISTS motion_events_seen_index ON motion_events (Seen)");
