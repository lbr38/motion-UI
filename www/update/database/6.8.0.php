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
