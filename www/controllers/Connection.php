<?php

namespace Controllers;

class Connection
{
    private $model;

    public function __construct()
    {
        $this->model = new \Models\Connection();
    }

    /**
     *  Retourne true si le résultat est vide et false si il est non-vide.
     */
    public function isempty($result)
    {
        return $this->model->isempty($result);
    }
}
