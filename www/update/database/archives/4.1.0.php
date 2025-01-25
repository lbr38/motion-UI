<?php
/**
 *  4.1.0 database update
 */

$vacuum = false;

/**
 *  Add Timelapse_interval column if not exist
 */
if (!$this->db->columnExist('settings', 'Timelapse_interval')) {
    $this->db->exec("ALTER TABLE settings ADD COLUMN Timelapse_interval INTEGER");
    $vacuum = true;

    /**
     *  Set default value to 300
     */
    $this->db->exec("UPDATE settings SET Timelapse_interval = '300'");
}

/**
 *  Add Timelapse_enabled column if not exist
 */
if (!$this->db->columnExist('cameras', 'Timelapse_enabled')) {
    $this->db->exec("ALTER TABLE cameras ADD COLUMN Timelapse_enabled CHAR(5)");
    $vacuum = true;

    /**
     *  Set default value to false
     */
    $this->db->exec("UPDATE cameras SET Timelapse_enabled = 'false'");
}

if ($vacuum === true) {
    $this->db->exec("VACUUM");
}
