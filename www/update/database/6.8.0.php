<?php
/**
 *  6.8.0 update
 */

/**
 *  Add 'Details' column to logs table
 */
if (!$this->db->columnExist('logs', 'Details')) {
    $this->db->exec("ALTER TABLE logs ADD COLUMN Details TEXT");
}

/**
 *  Drop motion_status_index index
 */
$this->db->exec("DROP INDEX IF EXISTS motion_status_index");

/**
 *  Remove 'Date' column from motion_status table
 */
if ($this->db->columnExist('motion_status', 'Date')) {
    $this->db->exec("ALTER TABLE motion_status DROP COLUMN Date");
}

/**
 *  Remove 'Time' column from motion_status table
 */
if ($this->db->columnExist('motion_status', 'Time')) {
    $this->db->exec("ALTER TABLE motion_status DROP COLUMN Time");
}

/**
 *  Add 'Timestamp' column to motion_status table
 */
if (!$this->db->columnExist('motion_status', 'Timestamp')) {
    $this->db->exec("ALTER TABLE motion_status ADD COLUMN Timestamp VARCHAR(255)");
}

/**
 *  Clean all lines from motion_status table
 */
$this->db->exec("DELETE FROM motion_status");

/**
 *  Recreate motion_status_index index
 */
$this->db->exec("CREATE INDEX motion_status_index ON motion_status (Timestamp, Status)");
