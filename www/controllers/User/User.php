<?php

namespace Controllers\User;

use Controllers\Utils\Random;
use Controllers\Utils\Validate;
use Exception;

class User
{
    protected $model;
    private $validRoles = ['administrator', 'usage'];
    private $rolesId = ['administrator' => 2, 'usage' => 3];

    public function __construct()
    {
        $this->model = new \Models\User\User();
    }

    /**
     *  Get username informations
     */
    public function get(int $id) : array
    {
        return $this->model->get($id);
    }

    /**
     *  Get users list from database
     */
    public function getUsers() : array
    {
        return $this->model->getUsers();
    }

    /**
     *  Get all users email from database
     */
    public function getEmails() : array
    {
        return array_unique($this->model->getEmails());
    }

    /**
     *  Get username by user Id
     */
    public function getUsernameById(string $id) : string
    {
        return $this->model->getUsernameById($id);
    }

    /**
     *  Get role by user Id
     */
    public function getRoleById(string $id) : string
    {
        return $this->model->getRoleById($id);
    }

    /**
     *  Get user Id by username
     */
    public function getIdByUsername(string $username) : int|null
    {
        return $this->model->getIdByUsername($username);
    }

    /**
     *  Add a new user in database and return the generated password
     */
    public function add(string $username, string $role) : string
    {
        $username = Validate::string($username);
        $role = Validate::string($role);

        /**
         *  Check that username does not contain invalid characters
         */
        if (Validate::alphaNumericHyphen($username) === false) {
            throw new Exception('Username cannot contain special characters except hyphen and underscore');
        }

        /**
         *  Check that user role is valid
         */
        if (!in_array($role, $this->validRoles)) {
            throw new Exception('Selected user role is invalid');
        }

        /**
         *  Check that username does not already exist
         */
        if ($this->exists($username)) {
            throw new Exception('Username ' . $username . ' already exists');
        }

        /**
         *  Generating a new random password
         */
        $password = Random::strongString(32);

        /**
         *  Hashing password with a salt automatically generated
         */
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        if ($password_hashed === false) {
            throw new Exception('Error while hashing user password');
        }

        /**
         *  Converting role as Id
         */
        $roleId = $this->rolesId[$role];

        /**
         *  Insert new user in database
         */
        $this->model->add($username, $password_hashed, $roleId);

        /**
         *  Return temporary generated password
         */
        return $password;
    }

    /**
     *  Delete specified user
     */
    public function delete(string $id) : void
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to execute this action.');
        }

        if (!$this->existsId($id)) {
            throw new Exception('Specified user does not exist');
        }

        $this->model->delete($id);
    }

    /**
     *  Return true if user Id exists
     */
    public function existsId(int $id) : bool
    {
        return $this->model->existsId($id);
    }

    /**
     *  Return true if user username exists
     */
    public function exists(string $username) : bool
    {
        return $this->model->exists($username);
    }

    /**
     *  Check that specified username / password couple matches with database
     */
    protected function checkUsernamePwd(int $id, string $password) : void
    {
        /**
         *  Check that the user exists
         */
        if ($this->existsId($id) === false) {
            throw new Exception('User does not exist');
        }

        /**
         *  Get user hashed password from database
         */
        $hashedPassword = $this->getHashedPasswordFromDb($id);

        /**
         *  If result is empty then it is anormal
         */
        if (empty($hashedPassword)) {
            throw new Exception('An error occured while checking user password');
        }

        /**
         *  If specified password does not matche database passord, then it is invalid
         */
        if (!password_verify($password, $hashedPassword)) {
            throw new Exception('Bad password');
        }
    }

    /**
     *  Get specified user hashed password
     */
    protected function getHashedPasswordFromDb(int $id) : string
    {
        return $this->model->getHashedPasswordFromDb($id);
    }
}
