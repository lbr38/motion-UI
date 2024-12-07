<?php

namespace Models;

use Exception;

abstract class Model
{
    protected $db;

    public function getConnection(string $database)
    {
        $this->db = new Connection($database);
    }

    /**
     *  Retourne l'Id de la dernière ligne insérée en base de données
     */
    public function getLastInsertRowID()
    {
        return $this->db->lastInsertRowID();
    }

    public function closeConnection()
    {
        $this->db->close();
    }
}
