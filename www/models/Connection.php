<?php

namespace Models;

use SQLite3;

class Connection extends SQLite3
{
    public function __construct()
    {
        try {
            if (!is_dir(DB_DIR)) {
                if (!mkdir(DB_DIR, 0777, true)) {
                    throw new Exception('Unable to create database directory');
                }
            }

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
            $this->busyTimeout(10000);
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
         *  Create live (cameras) table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS cameras (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        Name VARCHAR(255) NOT NULL,
        Url VARCHAR(255) NOT NULL,
        Stream_url VARCHAR(255),
        Output_type CHAR(5), /* image, video */
        Output_resolution VARCHAR(255),
        Refresh INTEGER,
        Rotate INTEGER,
        Text_left VARCHAR(255),
        Text_right VARCHAR(255),
        Live_enabled CHAR(5),
        Motion_enabled CHAR(5),
        Username VARCHAR(255),
        Password VARCHAR(255))");

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
        Motion_id_event_short INTEGER NOT NULL,
        Date_start DATE NOT NULL,
        Time_start TIME NOT NULL,
        Date_end DATE,
        Time_end TIME,
        Camera_id INTEGER,
        Status VARCHAR(10),
        Seen CHAR(5))");

        /**
         *  Create motion_events_files table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS motion_events_files (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        File VARCHAR(255) NOT NULL,
        Size VARCHAR(255),
        Width INTEGER,
        Height INTEGER,
        Fps INTEGER,
        Changed_pixels INTEGER,
        Motion_id_event INTEGER NOT NULL)");

        /**
         *  Create motion_status table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS motion_status (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        Date DATE NOT NULL,
        Time TIME NOT NULL,
        Status VARCHAR(8))");

        /**
         *  Create settings table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS settings (
        Motion_events_videos_thumbnail CHAR(5) NOT NULL,
        Motion_events_pictures_thumbnail CHAR(5) NOT NULL,
        Motion_events_retention INTEGER NOT NULL,
        Motion_advanced_edition_mode CHAR(5) NOT NULL)");

        /**
         *  If settings table is empty, fill it with default values
         */
        $result = $this->query("SELECT * FROM settings");
        if ($this->isempty($result) === true) {
            $this->exec("INSERT INTO settings 
            (Motion_events_videos_thumbnail, Motion_events_pictures_thumbnail, Motion_events_retention, Motion_advanced_edition_mode)
            VALUES ('true', 'true', '30', 'false')");
        }

        /**
         *  Create users table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS users (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        Username VARCHAR(255) NOT NULL,
        Password CHAR(60),
        First_name VARCHAR(50),
        Last_name VARCHAR(50),
        Email VARCHAR(100),
        Role INTEGER NOT NULL,
        Type CHAR(5) NOT NULL,
        State CHAR(7) NOT NULL)"); /* active / deleted */

        /**
         *  Create user_role table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS user_role (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        Name CHAR(15) NOT NULL UNIQUE)");

        /**
         *  If user_role table is empty, fill it with default values
         */
        $result = $this->query("SELECT Id FROM user_role");
        if ($this->isempty($result) === true) {
            /**
             *  super-administrator role (all perms)
             */
            $this->exec("INSERT INTO user_role ('Name') VALUES ('super-administrator')");
            /**
             *  administrator role
             */
            $this->exec("INSERT INTO user_role ('Name') VALUES ('administrator')");
            /**
             *  usage role
             */
            $this->exec("INSERT INTO user_role ('Name') VALUES ('usage')");
        }

        /**
         *  If users table is empty, then create admin user with default password 'motionui'
         */
        $result = $this->query("SELECT Id FROM users");

        if ($this->isempty($result) === true) {
            $password_hashed = '$2y$10$QjQqA7UwcxTJtBHYccyebOHRKw6P6YOARXCfsN1O.ZfBNoEkwWyFq';
            try {
                $stmt = $this->prepare("INSERT INTO users ('Username', 'Password', 'First_name', 'Role', 'State', 'Type') VALUES ('admin', :password_hashed, 'Administrator', '1', 'active', 'local')");
                $stmt->bindValue(':password_hashed', $password_hashed);
                $stmt->execute();
            } catch (\Exception $e) {
                \Controllers\Common::dbError($e);
            }
        }

        /**
         *  Generate layout_container_state table if not exists
         */
        $this->exec("CREATE TABLE IF NOT EXISTS layout_container_state (
        Container VARCHAR(255) NOT NULL,
        Id INTEGER NOT NULL)");

        /**
         *  Generate logs table if not exists
         */
        $this->exec("CREATE TABLE IF NOT EXISTS logs (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        Date DATE NOT NULL,
        Time TIME NOT NULL,
        Type CHAR(5) NOT NULL, /* info, error */
        Component VARCHAR(255),
        Message VARCHAR(255) NOT NULL,
        Status CHAR(9) NOT NULL)"); /* new, acquitted */

         /**
         *  Generate notifications table if not exists
         */
        $this->exec("CREATE TABLE IF NOT EXISTS notifications (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        Id_notification CHAR(5) NOT NULL,
        Title VARCHAR(255) NOT NULL,
        Message VARCHAR(255) NOT NULL,
        Status CHAR(9) NOT NULL)"); /* new, acquitted */

        /**
         *  Create indexes on tables with large amount of data
         */
        $this->exec("CREATE INDEX IF NOT EXISTS motion_events_index ON motion_events (Motion_id_event, Motion_id_event_short, Date_start, Time_start, Date_end, Time_end, Camera_id, Status, Seen)");
        $this->exec("CREATE INDEX IF NOT EXISTS motion_events_date_index ON motion_events (Date_start)");
        $this->exec("CREATE INDEX IF NOT EXISTS motion_events_files_index ON motion_events_files (File, Size, Width, Height, Fps, Changed_pixels, Motion_id_event)");
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

    /**
     *  Return true if column name exists in the specified table
     */
    public function columnExist(string $tableName, string $columnName)
    {
        $columns = array();

        $result = $this->query("PRAGMA table_info($tableName)");

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $columns[] = $row;
        }

        foreach ($columns as $column) {
            if ($column['name'] == $columnName) {
                return true;
            }
        }

        return false;
    }
}
