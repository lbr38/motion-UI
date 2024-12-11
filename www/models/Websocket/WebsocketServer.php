<?php

namespace Models\Websocket;

use Exception;

class WebsocketServer extends \Models\Model
{
    public function __construct()
    {
        /**
         *  Open database
         */
        $this->getConnection('ws');
    }

    /**
     *  Clean ws connections from database
     */
    public function cleanWsConnections()
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM ws_connections");
            $result = $stmt->execute();
        } catch (\Exception $e) {
            $this->db->logError($e);
        }
    }

    /**
     *  Add new ws connection in database
     */
    public function newWsConnection(int $connectionId)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO ws_connections ('Connection_id', 'Authenticated') VALUES (:id, 'false')");
            $stmt->bindValue(':id', $connectionId);
            $result = $stmt->execute();
        } catch (\Exception $e) {
            $this->db->logError($e);
        }
    }

    /**
     *  Set websocket connection type
     */
    public function setWsConnectionType(int $connectionId, string $type)
    {
        try {
            $stmt = $this->db->prepare("UPDATE ws_connections SET Type = :type WHERE Connection_id = :id");
            $stmt->bindValue(':id', $connectionId);
            $stmt->bindValue(':type', $type);
            $result = $stmt->execute();
        } catch (\Exception $e) {
            $this->db->logError($e);
        }
    }

    /**
     *  Return all authenticated websocket connections from database
     */
    public function getAuthenticatedWsConnections()
    {
        $connections = array();

        try {
            $stmt = $this->db->prepare("SELECT * FROM ws_connections WHERE Authenticated = 'true'");
            $result = $stmt->execute();
        } catch (\Exception $e) {
            $this->db->logError($e);
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $connections[] = $row;
        }

        return $connections;
    }

    /**
     *  Return all websocket connections from database
     */
    public function getWsConnections(string|null $type = null)
    {
        $connections = array();

        try {
            // If a connection type is provided, return only connections of that type
            if (!empty($type)) {
                $stmt = $this->db->prepare("SELECT * FROM ws_connections WHERE Type = :type");
                $stmt->bindValue(':type', $type);
            } else {
                $stmt = $this->db->prepare("SELECT * FROM ws_connections");
            }

            $result = $stmt->execute();
        } catch (\Exception $e) {
            $this->db->logError($e);
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $connections[] = $row;
        }

        return $connections;
    }

    /**
     *  Delete ws connection from database
     */
    public function deleteWsConnection(int $connectionId)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM ws_connections WHERE Connection_id = :id");
            $stmt->bindValue(':id', $connectionId);
            $result = $stmt->execute();
        } catch (\Exception $e) {
            $this->db->logError($e);
        }
    }
}