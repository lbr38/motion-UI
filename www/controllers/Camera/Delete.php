<?php

namespace Controllers\Camera;

use Exception;

class Delete extends Camera
{
    /**
     *  Delete camera
     */
    public function delete(string $id) : void
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to delete a camera');
        }

        $cameraStreamController = new \Controllers\Camera\Stream();

        /**
         *  Check if camera Id exist
         */
        if (!$this->existId($id)) {
            throw new Exception('Camera does not exist');
        }

        /**
         *  Delete camera in database
         */
        $this->model->delete($id);

        /**
         *  Delete camera config file
         */
        if (file_exists(CAMERAS_MOTION_CONF_ENABLED_DIR . '/camera-' . $id . '.conf')) {
            if (!unlink(CAMERAS_MOTION_CONF_ENABLED_DIR . '/camera-' . $id . '.conf')) {
                throw new Exception('Could not delete camera config file: ' . CAMERAS_MOTION_CONF_ENABLED_DIR . '/camera-' . $id . '.conf');
            }

            // Trigger motion restart if camera config file was enabled
            $this->motionServiceController->restart();
        }
        if (file_exists(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf')) {
            if (!unlink(CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf')) {
                throw new Exception('Could not delete camera config file: ' . CAMERAS_MOTION_CONF_AVAILABLE_DIR . '/camera-' . $id . '.conf');
            }
        }

        /**
         *  Delete camera data directory (timelapse images)
         */
        if (is_dir(CAMERAS_TIMELAPSE_DIR . '/camera-' . $id)) {
            if (!\Controllers\Filesystem\Directory::deleteRecursive(CAMERAS_TIMELAPSE_DIR . '/camera-' . $id)) {
                throw new Exception('Could not delete camera data directory: ' . CAMERAS_TIMELAPSE_DIR . '/camera-' . $id);
            }
        }

        /**
         *  Remove stream from go2rtc
         *  Do not restart go2rtc to avoid unnecessary restarts
         */
        $this->go2rtcController->removeStream($id, false);

        /**
         *  Remove camera Id from grid order
         */
        $cameraStreamController->removeFromOrder($id);

        unset($cameraStreamController);
    }
}
