<?php
/**
 *  5.3.0 update
 */

/**
 *  Delete 'Id' column from 'layout_container_state' table
 */
if ($this->db->columnExist('layout_container_state', 'Id') === true) {
    $this->db->exec("ALTER TABLE layout_container_state DROP COLUMN Id");
    $this->db->exec("VACUUM");
}
