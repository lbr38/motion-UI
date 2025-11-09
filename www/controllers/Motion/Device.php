<?php

namespace Controllers\Motion;

use Controllers\Utils\Validate;
use Exception;

class Device
{
    private $model;

    public function __construct()
    {
        $this->model = new \Models\Motion\Device();
    }

    /**
     *  Add a new device name and ip address to known devices
     */
    public function add(string $name, string $ip)
    {
        $name = Validate::string($name);
        $ip = Validate::string($ip);

        $this->model->add($name, $ip);
    }

    /**
     *  Remove a known device
     */
    public function remove(int $id)
    {
        if (!is_numeric($id)) {
            throw new Exception('Invalid device id');
        }

        $this->model->remove($id);
    }
}
