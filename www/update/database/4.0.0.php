<?php
/**
 *  4.0.0 database update
 */

$vacuum = false;

/**
 *  Delete columns from settings table
 */
if ($this->db->columnExist('settings', 'Motion_start_btn') === true) {
    $this->db->exec("ALTER TABLE settings DROP COLUMN Motion_start_btn");
    $vacuum = true;
}

if ($this->db->columnExist('settings', 'Motion_autostart_btn') === true) {
    $this->db->exec("ALTER TABLE settings DROP COLUMN Motion_autostart_btn");
    $vacuum = true;
}

if ($this->db->columnExist('settings', 'Motion_alert_btn') === true) {
    $this->db->exec("ALTER TABLE settings DROP COLUMN Motion_alert_btn");
    $vacuum = true;
}

if ($this->db->columnExist('settings', 'Motion_events') === true) {
    $this->db->exec("ALTER TABLE settings DROP COLUMN Motion_events");
    $vacuum = true;
}

if ($this->db->columnExist('settings', 'Motion_stats') === true) {
    $this->db->exec("ALTER TABLE settings DROP COLUMN Motion_stats");
    $vacuum = true;
}

if ($this->db->columnExist('settings', 'Stream_on_main_page') === true) {
    $this->db->exec("ALTER TABLE settings DROP COLUMN Stream_on_main_page");
    $vacuum = true;
}

if ($this->db->columnExist('settings', 'Stream_on_live_page') === true) {
    $this->db->exec("ALTER TABLE settings DROP COLUMN Stream_on_live_page");
    $vacuum = true;
}

/**
 *  Add Text_left column if not exist
 */
if (!$this->db->columnExist('cameras', 'Text_left') === true) {
    $this->db->exec("ALTER TABLE cameras ADD COLUMN Text_left VARCHAR(255)");
    $vacuum = true;
}

/**
 *  Add Text_right column if not exist
 */
if (!$this->db->columnExist('cameras', 'Text_right') === true) {
    $this->db->exec("ALTER TABLE cameras ADD COLUMN Text_right VARCHAR(255)");
    $vacuum = true;
}

/**
 *  Add Seen column if not exist
 */
if (!$this->db->columnExist('motion_events', 'Seen') === true) {
    $this->db->exec("ALTER TABLE motion_events ADD COLUMN Seen CHAR(5)");
    $vacuum = true;

    /**
     *  Set default value for Seen column
     */
    $this->db->exec("UPDATE motion_events SET Seen = 'true'");

    /**
     *  Recreate index
     */
    $this->db->exec("DROP INDEX IF EXISTS motion_events_index");
    $this->db->exec("CREATE INDEX IF NOT EXISTS motion_events_index ON motion_events (Motion_id_event, Motion_id_event_short, Date_start, Time_start, Date_end, Time_end, Camera_id, Status, Seen)");
}

if ($vacuum === true) {
    $this->db->exec("VACUUM");
}
