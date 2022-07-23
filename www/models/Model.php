<?php

namespace Models;

use Exception;

abstract class Model
{
    protected $db;

    /**
     *  Nouvelle connexion à la base de données
     */
    public function __construct()
    {
        $this->db = new Connection();
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
