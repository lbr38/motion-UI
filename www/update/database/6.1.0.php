<?php
/**
 *  6.1.0 update
 */

/**
 *  Add 'Stream_default_technology' column to settings table
 */
if (!$this->db->columnExist('settings', 'Stream_default_technology')) {
    $this->db->exec("ALTER TABLE settings ADD COLUMN Stream_default_technology VARCHAR(255) DEFAULT 'mse'");
}
