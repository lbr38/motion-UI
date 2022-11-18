<?php

namespace Controllers;

use Exception;

class Update
{
    private $model;
    private $workingDir = '/tmp/motionui-update_' . GIT_VERSION;

    public function __construct()
    {
        $this->model = new \Models\Update();
    }

    /**
     *  Acquit update log window, delete update log files
     */
    public function acquit()
    {
        if (file_exists(UPDATE_SUCCESS_LOG)) {
            unlink(UPDATE_SUCCESS_LOG);
        }

        if (file_exists(UPDATE_ERROR_LOG)) {
            unlink(UPDATE_ERROR_LOG);
        }
    }

    /**
     *  Enable / disable maintenance
     */
    public function setMaintenance(string $status)
    {
        if ($status == 'on') {
            /**
             *  Create 'update-running' file to enable maintenance page on the site
             */
            if (!file_exists(DATA_DIR . "/update-running")) {
                touch(DATA_DIR . "/update-running");
            }
        }

        if ($status == 'off') {
            if (file_exists(DATA_DIR . "/update-running")) {
                unlink(DATA_DIR . "/update-running");
            }
        }
    }

    /**
     *  Download new release tar archive
     */
    private function download()
    {
        /**
         *  Try to download release tar
         */
        if (!copy('https://github.com/lbr38/motion-UI/releases/download/' . GIT_VERSION . '/motion-UI_' . GIT_VERSION . '.tar.gz', $this->workingDir . '/motion-UI_' . GIT_VERSION . '.tar.gz')) {
            throw new Exception('Error while downloading new release.');
        }

        /**
         *  Extract archive
         */
        exec('tar xzf ' . $this->workingDir . '/motion-UI_' . GIT_VERSION . '.tar.gz -C ' . $this->workingDir . '/', $output, $return);
        if ($return != 0) {
            throw new Exception('Error while extracting new release archive.');
        }
    }

    /**
     *  Execute SQL queries to update database
     */
    public function updateDB()
    {
        $this->sqlQueriesDir = ROOT . '/update/database';

        if (!is_dir($this->sqlQueriesDir)) {
            return;
        }

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

    /**
     *  Update web source files
     */
    private function updateWeb()
    {
        /**
         *  Delete actual web root dir content
         */
        if (is_dir(ROOT)) {
            exec('rm -rf ' . ROOT . '/*', $output, $return);
            if ($return != 0) {
                throw new Exception('Error while deleting web root content <b>' . ROOT . '</b>');
            }
        }

        /**
         *  Copy new files to web root dir
         */
        exec("\cp -r " . $this->workingDir . '/motion-UI/www/* ' . ROOT . '/', $output, $return);
        if ($return != 0) {
            throw new Exception('Error while copying <b>' . $this->workingDir . '/motion-UI/www/</b> content to <b>' . ROOT . '/</b>');
        }

        /**
         *  Delete actual data dir tools content
         */
        if (is_dir(DATA_DIR . '/tools')) {
            exec('rm -rf ' . DATA_DIR . '/tools', $output, $return);
            if ($return != 0) {
                throw new Exception('Error while deleting tools directory content <b>' . DATA_DIR . '/tools/</b>');
            }
        }

        /**
         *  Copy new tools dir content
         */
        exec("\cp -r " . $this->workingDir . '/motion-UI/tools ' . DATA_DIR . '/', $output, $return);
        if ($return != 0) {
            throw new Exception('Error while copying <b>' . $this->workingDir . '/motion-UI/tools</b> to <b>' . DATA_DIR . '/</b>');
        }

        /**
         *  Delete actual motionui bash script
         */
        if (is_file(DATA_DIR . '/motionui')) {
            if (!unlink(DATA_DIR . '/motionui')) {
                throw new Exception('Error while deleting motionui bash script <b>' . DATA_DIR . '/motionui</b>');
            }
        }

        /**
         *  Copy new motionui bash script
         */
        exec("\cp " . $this->workingDir . '/motion-UI/motionui ' . DATA_DIR . '/motionui', $output, $return);
        if ($return != 0) {
            throw new Exception('Error while copying <b>' . $this->workingDir . '/motion-UI/motionui</b> to <b>' . DATA_DIR . '/motionui</b>');
        }
    }

    /**
     *  Execute update
     */
    public function update()
    {
        try {
            if (!is_dir(LOGS_DIR . '/update')) {
                mkdir(LOGS_DIR . '/update', 0770, true);
            }

            /**
             *  Delete old log files if exist
             */
            if (file_exists(UPDATE_ERROR_LOG)) {
                unlink(UPDATE_ERROR_LOG);
            }
            if (file_exists(UPDATE_SUCCESS_LOG)) {
                unlink(UPDATE_SUCCESS_LOG);
            }

            /**
             *  Quit if actual version is the same as the available version
             */
            if (VERSION == GIT_VERSION) {
                throw new Exception('Already up to date');
            }

            /**
             *  Enable maintenance page
             */
            $this->setMaintenance('on');

            /**
             *  Delete working dir if already exist
             */
            if (is_dir($this->workingDir)) {
                exec("rm '$this->workingDir' -rf", $output, $return);
                if ($return != 0) {
                    throw new Exception('Error while deleting old working directory <b>' . $this->workingDir . '</b>');
                }
            }

            /**
             *  Then create it
             */
            if (!mkdir($this->workingDir, 0770, true)) {
                throw new Exception('Error while trying to create working directory <b>' . $this->workingDir . '</b>');
            }

            /**
             *  Download new release
             */
            $this->download();

            /**
             *  Update web source files
             */
            $this->updateWeb();

            /**
             *  Apply database update queries if there are
             */
            $this->updateDB();

            /**
             *  Set permissions on motionui service script
             */
            if (!chmod(DATA_DIR . '/tools/service/motionui-service', octdec('0550'))) {
                throw new Exception('Error while trying to set permissions on <b>' . DATA_DIR . '/tools/service/motionui-service</b>');
            }
            chmod(DATA_DIR . '/tools/event', octdec("0550"));
            chmod(DATA_DIR . '/motionui', octdec("0550"));
            chgrp(DATA_DIR . '/tools/event', 'motionui');

            /**
             *  Delete working dir
             */
            if (is_dir($this->workingDir)) {
                exec("rm '$this->workingDir' -rf", $output, $return);
                if ($return != 0) {
                    throw new Exception('Error while cleaning working directory <b>' . $this->workingDir . '</b>');
                }
            }

            /**
             *  Create a file to restart motionui service
             */
            if (!file_exists(DATA_DIR . '/service.restart')) {
                touch(DATA_DIR . '/service.restart');
            }

            /**
             *  Write to success log to file
             */
            $updateJSON = json_encode(array('Version' => GIT_VERSION, 'Message' => 'Update successful'));
            file_put_contents(UPDATE_SUCCESS_LOG, $updateJSON);
        } catch (Exception $e) {
            /**
             *  Write to error log to file
             */
            $updateJSON = json_encode(array('Version' => GIT_VERSION, 'Message' => 'Error while updating motion-UI: ' . $e->getMessage()));

            file_put_contents(UPDATE_ERROR_LOG, $updateJSON);
        }

        /**
         *  Disable maintenance page
         */
        $this->setMaintenance('off');
    }
}
