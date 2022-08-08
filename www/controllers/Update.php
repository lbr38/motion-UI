<?php

namespace Controllers;

use Exception;

class Update
{
    private $model;
    private $workingDir;
    private $log = array();

    public function __construct()
    {
        $this->model = new \Models\Update();
    }

    /**
     *  Download new release tar archive
     */
    private function download()
    {
        /**
         *  Quit if wget if not installed
         */
        if (!file_exists('/usr/bin/wget')) {
            throw new Exception('/usr/bin/wget not found.');
        }

        exec('wget --no-cache -q "https://github.com/lbr38/motion-UI/releases/download/' . GIT_VERSION . '/motion-UI_' . GIT_VERSION . '.tar.gz" -O "' . $this->workingDir . '/motion-UI_' . GIT_VERSION . '.tar.gz"', $output, $return);
        if ($return != 0) {
            $this->log[] = $output;
            throw new Exception('Error while downloading new release.');
        }

        /**
         *  Extract archive
         */
        exec('tar xzf ' . $this->workingDir . '/motion-UI_' . GIT_VERSION . '.tar.gz -C ' . $this->workingDir . '/', $output, $return);
        if ($return != 0) {
            $this->log[] = $output;
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
         *  Delete actual root webdir and copy it from the new release
         */
        if (is_dir(ROOT)) {
            exec("rm -rf '" . ROOT . "/*");
        }

        exec("\cp -r " . $this->workingDir . '/motion-UI/www/* ' . ROOT . '/', $output, $return);
        if ($return != 0) {
            $this->log[] = $output;
            throw new Exception('Error while copying <b>' . $this->workingDir . '/motion-UI/www</b> to <b>' . ROOT . '/</b>');
        }

        /**
         *  Copy scripts and tools to datadir
         */
        if (is_dir(DATA_DIR . '/tools')) {
            exec("rm -rf " . DATA_DIR . '/tools');
        }
        exec("\cp -r " . $this->workingDir . '/motion-UI/tools ' . DATA_DIR . '/', $output, $return);
        if ($return != 0) {
            $this->log[] = $output;
            throw new Exception('Error while copying <b>' . $this->workingDir . '/motion-UI/tools</b> to <b>' . DATA_DIR . '/</b>');
        }

        if (is_file(DATA_DIR . '/motionui')) {
            unlink(DATA_DIR . '/motionui');
        }
        exec("\cp " . $this->workingDir . '/motion-UI/motionui ' . DATA_DIR . '/motionui', $output, $return);
        if ($return != 0) {
            $this->log[] = $output;
            throw new Exception('Error while copying <b>' . $this->workingDir . '/motion-UI/motionui</b> to <b>' . DATA_DIR . '/motionui</b>');
        }
    }

    /**
     *  Execute update
     */
    public function update()
    {
        try {
            /**
             *  Quit if actual version is the same as the available version
             */
            if (VERSION == GIT_VERSION) {
                return '<span>Already up to date.</span>';
            }

            /**
             *  Update
             */
            $this->workingDir = '/tmp/motion-UI-update_' . GIT_VERSION;

            /**
             *  Delete working dir if already exist
             */
            if (is_dir($this->workingDir)) {
                exec("rm '$this->workingDir' -rf");
            }

            /**
             *  Then create it
             */
            mkdir($this->workingDir, 0770, true);

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
             *  Apply permissions on motion-UI service script
             */
            chmod(DATA_DIR . '/tools/service/motionui-service', 550);

            /**
             *  Edit version file
             */
            file_put_contents(ROOT . 'version', GIT_VERSION);

            /**
             *  Delete working dir
             */
            exec("rm '$this->workingDir' -rf ");

            return '<span class="greentext">Update to ' . GIT_VERSION . ' successful</span>';
        } catch (Exception $e) {
            /**
             *  If an error occured, insert error log into update.log
             */
            if (!empty($this->log)) {
                if (!is_dir(DATA_DIR . '/logs/update')) {
                    mkdir(DATA_DIR . '/logs/update', 0770, true);
                }

                if (file_exists(DATA_DIR . '/logs/update/update.log')) {
                    unlink(DATA_DIR . '/logs/update/update.log');
                }

                touch(DATA_DIR . '/logs/update/update.log');

                foreach ($this->log as $log) {
                    file_put_contents(DATA_DIR . '/logs/update/update.log', $log . PHP_EOL, FILE_APPEND);
                }
            }

            /**
             *  Return catched error message
             */
            return '<span class="redtext">Error while updating to ' . GIT_VERSION . ': ' . $e->getMessage() . '</span>';
        }
    }
}
