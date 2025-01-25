<?php
/**
 *  3.1.6 database update
 */

/**
 *  Add new index
 */
$this->db->exec("CREATE INDEX IF NOT EXISTS motion_events_date_index ON motion_events (Date_start)");
$this->db->exec("VACUUM");
$this->db->exec("ANALYZE");
