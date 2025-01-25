<?php
/**
 *  4.2.0 database update
 */

$vacuum = false;

/**
 *  Add Timelapse_retention column if not exist
 */
if (!$this->db->columnExist('settings', 'Timelapse_retention')) {
    $this->db->exec("ALTER TABLE settings ADD COLUMN Timelapse_retention INTEGER");
    $vacuum = true;

    /**
     *  Set default value to 300
     */
    $this->db->exec("UPDATE settings SET Timelapse_retention = '30'");
}

if ($vacuum === true) {
    $this->db->exec("VACUUM");
}
