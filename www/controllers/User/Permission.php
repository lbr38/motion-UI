<?php

namespace Controllers\User;

use Exception;

class Permission
{
    private $model;
    private $userController;

    public function __construct()
    {
        $this->model = new \Models\User\Permission();
        $this->userController = new \Controllers\User\User();
    }

    /**
     *  Get user permissions
     */
    public function get(int $id) : array
    {
        $permissions = $this->model->get($id);

        /**
         *  Decode permissions (JSON) and return them
         */
        try {
            $permissions = json_decode($permissions, true, 512, JSON_THROW_ON_ERROR);
            return $permissions;
        } catch (Exception $e) {
            throw new Exception('error decoding permissions: ' . $e->getMessage());
        }
    }

    /**
     *  Set user permissions
     */
    private function set(int $id, string $permissions) : void
    {
        $this->model->set($id, $permissions);
    }

    /**
     *  Delete user permissions
     */
    public function delete(int $id) : void
    {
        $this->model->delete($id);
    }

    /**
     *  Grant camera access to user
     */
    public function grantCameraAccess(int $id, array $cameras = []) : void
    {
        $permissions = ['cameras_access' => []];

        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to execute this action.');
        }

        /**
         *  Check that user exists
         */
        if (!$this->userController->existsId($id)) {
            throw new Exception('User does not exist');
        }

        try {
            /**
             *  Get current user permissions
             */
            $permissions = $this->get($id);

            /**
             *  Grant access to selected cameras
             */
            $permissions['cameras_access'] = $cameras;

            /**
             *  Encode permissions (JSON)
             */
            try {
                $permissions = json_encode($permissions, JSON_THROW_ON_ERROR);
            } catch (Exception $e) {
                throw new Exception('could not write permissions: ' . $e->getMessage());
            }

            /**
             *  Set permissions in database
             */
            $this->set($id, $permissions);
        } catch (Exception $e) {
            throw new Exception('Could not grant camera access: ' . $e->getMessage());
        }
    }
}
