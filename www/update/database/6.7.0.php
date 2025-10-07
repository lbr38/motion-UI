<?php
/**
 *  6.7.0 update
 */

/**
 *  Add 'Disk_usage' column to system_monitoring table
 */
if (!$this->db->columnExist('system_monitoring', 'Disk_usage')) {
    $this->db->exec("ALTER TABLE system_monitoring ADD COLUMN Disk_usage REAL");
}
