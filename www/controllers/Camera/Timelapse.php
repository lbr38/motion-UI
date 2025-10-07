<?php

namespace Controllers\Camera;

use Exception;

class Timelapse
{
    private $cameraController;

    public function __construct()
    {
        $this->cameraController = new \Controllers\Camera\Camera();
    }

    /**
     *  Return timelapse container
     */
    public function display(int $cameraId, string|null $date = null, string|null $picture = null) : string
    {
        ob_start();
        include(ROOT . '/views/includes/camera/timelapse.inc.php');
        $form = ob_get_clean();

        return $form;
    }
}
