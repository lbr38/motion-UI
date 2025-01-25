<?php

namespace Models;

use SQLite3;

class Connection extends SQLite3
{
    public function __construct(string $database = 'main')
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
            if ($database == 'main') {
                $this->open(DB);
                $this->busyTimeout(30000);
                $this->enableExceptions(true);
                $this->enableWAL();
                $this->generateTables();
            } elseif ($database == 'ws') {
                $this->open(WS_DB);
                $this->busyTimeout(30000);
                $this->enableExceptions(true);
                $this->enableWAL();
                $this->generateWsTables();
            } else {
                throw new Exception("unknown database: $database");
            }
        } catch (\Exception $e) {
            die('Error while trying to open database: ' . $e->getMessage());
        }
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
        /**
         *  Create live (cameras) table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS cameras (
        Id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        Configuration TEXT)");

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
        Timelapse_interval INTEGER NOT NULL,
        Timelapse_retention INTEGER NOT NULL,
        Motion_events_retention INTEGER NOT NULL)");

        /**
         *  If settings table is empty, fill it with default values
         */
        $result = $this->query("SELECT * FROM settings");
        if ($this->isempty($result) === true) {
            $this->exec("INSERT INTO settings 
            (Timelapse_interval, Timelapse_retention, Motion_events_retention)
            VALUES ('300', '30', '30')");
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
         *  Create user_permissions table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS user_permissions (
        Permissions VARCHAR(255),
        User_id INTEGER NOT NULL)");

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
                $this->logError($e->getMessage());
            }
        }

        /**
         *  Generate layout_container_state table if not exists
         */
        $this->exec("CREATE TABLE IF NOT EXISTS layout_container_state (
        Container VARCHAR(255) NOT NULL)");

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
        $this->exec("CREATE INDEX IF NOT EXISTS motion_events_seen_index ON motion_events (Seen)");
        $this->exec("CREATE INDEX IF NOT EXISTS motion_events_files_index ON motion_events_files (File, Size, Width, Height, Fps, Changed_pixels, Motion_id_event)");
        $this->exec("CREATE INDEX IF NOT EXISTS motion_events_files_motion_id_event_index ON motion_events_files (Motion_id_event)");
    }

    /**
     *  Generate tables in the ws database
     */
    private function generateWsTables()
    {
        /**
         *  ws_connections table
         */
        $this->exec("CREATE TABLE IF NOT EXISTS ws_connections (
        Connection_id INTEGER,
        Type VARCHAR(255),
        Authenticated CHAR(5))"); /* true, false */

        // ws_connections table indexes:
        $this->exec("CREATE INDEX IF NOT EXISTS ws_connections_type ON ws_connections (Type)");
        $this->exec("CREATE INDEX IF NOT EXISTS ws_connections_authenticated ON ws_connections (Authenticated)");
        $this->exec("CREATE INDEX IF NOT EXISTS ws_connections_connection_id ON ws_connections (Connection_id)");
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

    /**
     *  Log a database error in database
     */
    public function logError(string $exception = null)
    {
        $logController = new \Controllers\Log\Log();

        if (!empty($exception)) {
            $logController->log('error', 'Database', 'An error occured while executing request in database.', $exception);
        } else {
            $logController->log('error', 'Database', 'An error occured while executing request in database.');
        }
    }
}
