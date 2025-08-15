<?php
/**
 *  6.6.0 update
 */

/**
 *  Create cameras_sort table if it does not exist
 */
$this->db->exec("CREATE TABLE IF NOT EXISTS cameras_sort (
'Order' VARCHAR(255))");
