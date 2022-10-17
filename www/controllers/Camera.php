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
     *  Prints camera
     */
    public function display(string $id)
    {
        /**
         *  Get camera configuration (url,..)
         */
        $this->getConfiguration($id);

        include('../includes/configure-camera.inc.php'); ?>

        <div id="camera<?= $id ?>-container">

            <h3><?= $this->name ?></h3>

            <div class="loading-camera-image">
                <button class="btn-square-none"><img src="resources/icons/loading.gif" class="icon" title="Loading image" /></button>
                <span class="block center lowopacity">Loading image</span>
            </div>

            <div id="camera<?= $id ?>-image" class="camera-image">
                <?php
                /**
                 *  Get image from http camera
                 */
                //$this->downloadImage($this->id, $this->url); ?>

                <!-- Camera image -->
                <img src=".live/camera<?= $this->id ?>/image" style="transform:rotate(<?= $this->rotate ?>deg);">
            </div>

            <!-- Unavailable image div -->
            <div id="camera<?= $id ?>-unavailable" class="hide">
                <button class="btn-square-red"><img src="resources/icons/error-close.svg" class="icon" title="Unavailable" /></button>
                <span class="block center lowopacity">Unavailable</span>
            </div>

            <br>
            <div class="camera-btn-div">
                <div class="slide-btn configure-camera-btn" title="Configure" camera-id="<?= $id ?>">
                    <img src="resources/icons/cog.svg" />
                    <span>Configure</span>
                </div>

                <div class="slide-btn-red delete-camera-btn" title="Delete" camera-id="<?= $id ?>">
                    <img src="resources/icons/bin.svg" />
                    <span>Delete</span>
                </div>

                <div class="slide-btn-yellow full-screen-camera-btn" title="Full screen" camera-id="<?= $id ?>">
                    <img src="resources/icons/screen.svg" />
                    <span>Full screen</span>
                </div> 
                
                <button class="close-full-screen-camera-btn btn-medium-yellow" camera-id="<?= $id ?>">Close full screen</button>
            </div>

            <br>

            <script>
                $(document).ready(function(){
                    /**
                     *  Print loading image for 1sec and load image
                     */
                    setTimeout(function(){
                        reloadImage(<?= $id ?>);
                    }, 1000);
                });

                /**
                 *  Autorechargement des images
                 */
                <?php
                if (!empty($this->refresh)) :
                    $refreshTotal = $this->refresh * 1000; ?>
                    $(document).ready(function(){
                        setInterval(function(){

                            /**
                             *  Ajax call to get a new image from http camera
                             */
                            reloadImage(<?= $id ?>);
                        }, <?= $refreshTotal ?>);
                    });
                    <?php
                endif ?>
            </script>
        </div>
        <?php
    }

    /**
     *  Check if distant http camera is accessible
     */
    public function checkAvailability(string $url)
    {
        /**
         *  Checks if camera is available with a tiemout of 1sec
         *  To avoid long page loading when some cameras are offline
         */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);

        if (curl_errno($ch) != 0) {
            return false;
        }

        return true;
    }

    /**
     *  Download image from distant http camera
     */
    public function downloadImage(string $id, string $url)
    {
        if (!is_dir(ROOT . '/public/.live/camera' . $this->id)) {
            mkdir(ROOT . '/public/.live/camera' . $this->id, 0770, true);
        }
        if (!copy($url, ROOT . '/public/.live/camera' . $id . '/image')) {
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
         *  Check availability before
         */
        if ($this->checkAvailability($this->url) === false) {
            /**
             *  Throw exception that will make ajax print an 'unavailable div' for this camera
             */
            throw new Exception('Unavailable');
        }

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
