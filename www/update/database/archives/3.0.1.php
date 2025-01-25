<?php
/**
 *  3.0.1 database update
 */

/**
 *  Quit if Output_resolution column exists in cameras table
 */
if ($this->db->columnExist('cameras', 'Output_resolution') === true) {
    return;
}

/**
 *  Add Output_resolution column to cameras table
 */
$this->db->exec("ALTER TABLE cameras ADD Output_resolution VARCHAR(255)");

/**
 *  Fill Output_resolution column with default value
 */
$this->db->exec("UPDATE cameras SET Output_resolution = '640x480'");
