<?php
/**
 *  4.0.2 database update
 */

$vacuum = false;

/**
 *  Add Timestamp_left column if not exist
 */
if (!$this->db->columnExist('cameras', 'Timestamp_left')) {
    $this->db->exec("ALTER TABLE cameras ADD COLUMN Timestamp_left CHAR(5)");
    $vacuum = true;
}

/**
 *  Add Timestamp_right column if not exist
 */
if (!$this->db->columnExist('cameras', 'Timestamp_right')) {
    $this->db->exec("ALTER TABLE cameras ADD COLUMN Timestamp_right CHAR(5)");
    $vacuum = true;
}

if ($vacuum === true) {
    $this->db->exec("VACUUM");
}
