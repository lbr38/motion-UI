<?php

namespace Controllers;

use Exception;

class Camera
{
    private $id;
    private $name;
    private $url;
    private $rotate;
    private $refresh;
    private $curlHandle;

    public function __construct()
    {
        /**
         *  Initialize shared curl handle
         */
        $this->curlHandle = curl_init();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getRotate()
    {
        return $this->rotate;
    }

    public function getRefresh()
    {
        return $this->refresh;
    }

    /**
     *  Get camera's configuration
     */
    public function getConfiguration(string $id)
    {
        if (!file_exists(CAMERA_DIR . '/camera' . $id . '.conf')) {
            throw new Exception('The specified camera id does not exist');
        }

        $this->id = $id;

        /**
         *  Récupération de la configuration de la caméra
         */
        $configuration = file_get_contents(CAMERA_DIR . '/camera' . $id . '.conf');

        preg_match('/name=.*/', $configuration, $matche_name);
        preg_match('/url=.*/', $configuration, $matche_url);
        preg_match('/refresh=.*/', $configuration, $matche_refresh);
        preg_match('/rotate=.*/', $configuration, $matche_rotate);

        if (!empty($matche_name[0])) {
            $this->name = str_replace('name=', '', $matche_name[0]);
        } else {
            \Controllers\Common::printAlert("Camera $id name can't be found", 'error');
            $this->name = '';
        }
        if (!empty($matche_url[0])) {
            $this->url = str_replace('url=', '', $matche_url[0]);
        } else {
            \Controllers\Common::printAlert("Camera $id url can't be found", 'error');
            $this->url = '';
        }
        if (!empty($matche_refresh[0])) {
            $this->refresh = str_replace('refresh=', '', $matche_refresh[0]);
        } else {
            \Controllers\Common::printAlert("Camera $id refresh can't be found", 'error');
            $this->refresh = '';
        }
        if (!empty($matche_rotate[0])) {
            $this->rotate = str_replace('rotate=', '', $matche_rotate[0]);
        } else {
            \Controllers\Common::printAlert("Camera $id rotate can't be found", 'error');
            $this->rotate = '';
        }
    }

    /**
     *  Returns the total count of cameras
     */
    public function getTotal()
    {
        /**
         *  Get total camera config files
         */
        return count(glob(CAMERA_DIR . "/*.conf"));
    }

    /**
     *  Returns an array with all camera Id
     */
    public function getCamerasIds()
    {
        $cameraFiles = glob(CAMERA_DIR . "/*.conf");

        if (!empty($cameraFiles)) {
            $cameraIds = array();

            foreach ($cameraFiles as $cameraFile) {
                $cameraFile = basename($cameraFile);

                $cameraId = str_replace('camera', '', $cameraFile);
                $cameraId = str_replace('.conf', '', $cameraId);

                $cameraIds[] = $cameraId;
            }
        }

        return $cameraIds;
    }

    /**
     *  Add a new camera
     */
    public function add(string $name, string $url)
    {
        $cameraId = 1;

        /**
         *  Incrément le nouvel id de camera si celui-ci est déjà utilisé
         */
        while (file_exists(CAMERA_DIR . '/camera' . $cameraId . '.conf')) {
            $cameraId++;
        }

        $configuration = 'name=' . $name . PHP_EOL;
        $configuration .= 'url=' . $url . PHP_EOL;
        $configuration .= 'rotate=0' . PHP_EOL;
        $configuration .= 'refresh=5' . PHP_EOL;

        /**
         *  Ecriture de la nouvelle configuration
         */
        file_put_contents(CAMERA_DIR . '/camera' . $cameraId . '.conf', $configuration, FILE_APPEND);
    }

    /**
     *  Delete camera
     */
    public function delete(string $id)
    {
        if (!file_exists(CAMERA_DIR . '/camera' . $id . '.conf')) {
            throw new Exception('The specified camera id does not exist');
        }

        /**
         *  Delete the configuration file
         */
        unlink(CAMERA_DIR . '/camera' . $id . '.conf');
    }

    /**
     *  Edit camera configuration
     */
    public function edit(string $id, string $name, string $url, string $rotate, string $refresh)
    {
        if (!file_exists(CAMERA_DIR . '/camera' . $id . '.conf')) {
            throw new Exception('The specified camera id does not exist');
        }

        $configuration = 'name=' . $name . PHP_EOL;
        $configuration .= 'url=' . $url . PHP_EOL;
        $configuration .= 'rotate=' . $rotate . PHP_EOL;
        $configuration .= 'refresh=' . $refresh . PHP_EOL;

        /**
         *  Write new configuration
         */
        file_put_contents(CAMERA_DIR . '/camera' . $id . '.conf', $configuration);
    }

    /**
     *  Download image from distant http camera
     */
    public function downloadImage(string $id, string $url)
    {
        /**
         *  Create target dir if not exist
         */
        if (!is_dir(ROOT . '/public/resources/.live/camera' . $id)) {
            mkdir(ROOT . '/public/resources/.live/camera' . $id, 0770, true);
        }

        $curlError = 0;
        $localFile = fopen(ROOT . '/public/resources/.live/camera' . $id . '/image.jpg', "w");

        curl_setopt($this->curlHandle, CURLOPT_URL, $url);            // set remote file url
        curl_setopt($this->curlHandle, CURLOPT_FILE, $localFile);     // set output file
        curl_setopt($this->curlHandle, CURLOPT_TIMEOUT, 2);           // set timeout
        curl_setopt($this->curlHandle, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($this->curlHandle, CURLOPT_FOLLOWLOCATION, true); // follow redirect
        curl_setopt($this->curlHandle, CURLOPT_ENCODING, '');         // use compression if any
        curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYPEER, false);

        curl_exec($this->curlHandle);

        /**
         *  If curl has failed (meaning a curl param might be invalid)
         */
        if (curl_errno($this->curlHandle)) {
            return false;
        }

        /**
         *  Check that the http return code is 200 (the file has been downloaded)
         */
        $status = curl_getinfo($this->curlHandle);

        if ($status["http_code"] != 200) {
            return false;
        }

        return true;
    }

    /**
     *  Reload image from http camera
     */
    public function reloadImage(string $id)
    {
        $this->getConfiguration($id);

        /**
         *  Try to download a new image
         */
        if ($this->downloadImage($this->id, $this->url) === false) {
            /**
             *  Throw exception that will make ajax print an 'unavailable div' for this camera
             */
            throw new Exception('Unavailable');
        }
    }
}
