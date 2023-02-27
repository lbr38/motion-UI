<?php

namespace Models;

use Exception;

class Update extends Model
{
    public function updateDB(string $updateFile)
    {
        if (!file_exists($updateFile)) {
            throw new Exception("Error: database update file '$updateFile' not found");
        }

        /**
         *  Include file to execute SQL queries in it.
         */
        include_once($updateFile);
    }
}
