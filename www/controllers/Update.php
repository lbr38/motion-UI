<?php

namespace Controllers;

use Exception;

class Update
{
    private $model;

    public function __construct()
    {
        $this->model = new \Models\Update();
    }

    /**
     *  Execute SQL queries to update database
     */
    public function updateDB(string $targetVersion = null)
    {
        $this->sqlQueriesDir = ROOT . '/update/database';

        if (!is_dir($this->sqlQueriesDir)) {
            return;
        }

        /**
         *  If a target release version is specified, only execute database update file that contains this version number
         */
        if (!empty($targetVersion)) {
            $updateFile = $this->sqlQueriesDir . '/' . $targetVersion . '.php';

            /**
             *  Execute file if exist
             */
            if (file_exists($updateFile)) {
                $this->model->updateDB($updateFile);
            }

            return;
        }

        /**
         *  Else execute all database update files
         */

        /**
         *  Get all the files
         */
        $updateFiles = glob($this->sqlQueriesDir . '/*.php');

        /**
         *  For each files found execute its queries
         */
        if (!empty($updateFiles)) {
            foreach ($updateFiles as $updateFile) {
                $this->model->updateDB($updateFile);
            }
        }
    }
}
