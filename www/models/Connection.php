<?php

namespace Models;

use SQLite3;

class Connection extends SQLite3
{
    public function __construct()
    {
        try {
            /**
             *  Open database
             */
            $this->open(DB);
            $this->enableExceptions(true);
        } catch (\Exception $e) {
            die('Error while trying to open database: ' . $e->getMessage());
        }

        /**
         *  Add a 5sec timeout to database opening
         */
        try {
            $this->busyTimeout(5000);
        } catch (\Exception $e) {
            die('Error while trying to configure database timeout: ' . $e->getMessage());
        }

        $this->generateTables();
    }

    /**
     *  Activate SQLite WAL mode
     */
    private function enableWAL()
    {
        $this->exec('pragma journal_mode = WAL;');
        $this->exec('pragma synchronous = normal;');
        $this->exec('pragma temp_store = memory;');
        $this->exec('pragma mmap_size = 30000000000;');
    }

    /**
     *  Generate tables if not exist
     */
    private function generateTables()
    {
        $this->enableWAL();

        /**
         *  Create alerts table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS alerts (
        Status CHAR(8),
        Recipient VARCHAR(255),
        Mutt_config VARCHAR(255),
        Monday_start CHAR(5),
        Monday_end CHAR(5) ,
        Tuesday_start CHAR(5),
        Tuesday_end CHAR(5),
        Wednesday_start CHAR(5),
        Wednesday_end CHAR(5),
        Thursday_start CHAR(5),
        Thursday_end CHAR(5),
        Friday_start CHAR(5),
        Friday_end CHAR(5),
        Saturday_start CHAR(5),
        Saturday_end CHAR(5),
        Sunday_start CHAR(5),
        Sunday_end CHAR(5))");

        /**
         *  If alerts table is empty, fill it with default values
         */
        $result = $this->query("SELECT Status FROM alerts");
        if ($this->isempty($result) === true) {
            $this->exec("INSERT INTO alerts (Status) VALUES ('disabled')");
        }

        /**
         *  Create autostart table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS autostart (
        Status CHAR(8),
        Device_presence CHAR(8),
        Monday_start CHAR(5),
        Monday_end CHAR(5),
        Tuesday_start CHAR(5),
        Tuesday_end CHAR(5),
        Wednesday_start CHAR(5),
        Wednesday_end CHAR(5),
        Thursday_start CHAR(5),
        Thursday_end CHAR(5),
        Friday_start CHAR(5),
        Friday_end CHAR(5),
        Saturday_start CHAR(5),
        Saturday_end CHAR(5),
        Sunday_start CHAR(5),
        Sunday_end CHAR(5))");

        /**
         *  If autostart table is empty, fill it with default values
         */
        $result = $this->query("SELECT Status FROM autostart");
        if ($this->isempty($result) === true) {
            $this->exec("INSERT INTO autostart (Status, Device_presence) VALUES ('disabled', 'disabled')");
        }

        /**
         *  Create devices table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS devices (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        Name VARCHAR(255) NOT NULL,
        Ip VARCHAR(15) NOT NULL)");

        /**
         *  Create motion_events table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS motion_events (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        Motion_id_event INTEGER NOT NULL,
        Date_start DATE NOT NULL,
        Time_start TIME NOT NULL,
        Date_end DATE,
        Time_end TIME,
        Camera_id INTEGER,
        Camera_name VARCHAR(255),
        Status VARCHAR(10))");

        /**
         *  Create motion_events_files table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS motion_events_files (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        File VARCHAR(255) NOT NULL,
        Id_event INTEGER NOT NULL)");

        /**
         *  Create settings table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS settings (
        Print_live_btn CHAR(3),
        Print_motion_start_btn CHAR(3),
        Print_motion_autostart_btn CHAR(3),
        Print_motion_alert_btn CHAR(3),
        Print_motion_stats CHAR(3),
        Print_motion_captures CHAR(3),
        Print_motion_config CHAR(3))");

        /**
         *  If settings table is empty, fill it with default values
         */
        $result = $this->query("SELECT Print_live_btn FROM settings");
        if ($this->isempty($result) === true) {
            $this->exec("INSERT INTO settings 
            (Print_live_btn, Print_motion_start_btn, Print_motion_autostart_btn, Print_motion_alert_btn, Print_motion_stats, Print_motion_captures, Print_motion_config)
            VALUES ('yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes')");
        }
    }

    /**
     *  Retourne true si le résultat est vide et false si il est non-vide.
     */
    public function isempty($result)
    {
        /**
         *  Compte le nombre de lignes retournées par la requête
         */
        $count = 0;

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $count++;
        }

        if ($count == 0) {
            return true;
        }

        return false;
    }
}
