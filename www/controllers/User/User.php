<?php

namespace Controllers\User;

use Exception;

class User
{
    private $model;
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
        $username = \Controllers\Common::validateData($username);
        $role = \Controllers\Common::validateData($role);

        /**
         *  Check that username does not contain invalid characters
         */
        if (\Controllers\Common::isAlphanumDash($username) === false) {
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
        $password = $this->generateRandomPassword();

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
     *  Reset user password
     */
    public function resetPassword(string $id) : string
    {
        if (!IS_ADMIN) {
            throw new Exception('You are not allowed to execute this action.');
        }

        if (!$this->existsId($id)) {
            throw new Exception('Specified user does not exist');
        }

        /**
         *  Get username
         */
        $username = $this->getUsernameById($id);
        $role = $this->getRoleById($id);

        /**
         *  If the current user is not a superadmin (he's only an admin), then he cannot reset password of another admin
         */
        if (!IS_SUPERADMIN) {
            if ($role == 2) {
                throw new Exception('You are not allowed to reset password of another administrator');
            }
        }

        /**
         *  Generating a new password
         */
        $password = $this->generateRandomPassword();

        /**
         *  Hashing password with salt
         */
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        if ($hashedPassword === false) {
            throw new Exception('Error while creating a new password for user' . $username);
        }

        /**
         *  Adding new hashed password in database
         */
        $this->updatePassword($id, $hashedPassword);

        /**
         *  Return new password
         */
        return $password;
    }

    /**
     *  Changing user password
     */
    public function changePassword(int $id, string $actualPassword, string $newPassword, string $newPasswordRetype) : void
    {
        /**
         *  Check that user exists
         */
        if ($this->existsId($id) === false) {
            throw new Exception('User does not exist');
        }

        /**
         *  Check that the provided Id matches the current user Id (in session)
         */
        if ($id != $_SESSION['id']) {
            throw new Exception('You are not allowed to change password of another user');
        }

        /**
         *  Check that the actual password is valid
         */
        $this->checkUsernamePwd($id, $actualPassword);

        /**
         *  Now checking that actual password matches actual password in database
         */

        /**
         *  Get actual hashed password in database
         */
        $actualPasswordHashed = $this->getHashedPasswordFromDb($id);

        /**
         *  If result is empty then it is anormal
         */
        if (empty($actualPasswordHashed)) {
            throw new Exception('An error occured while checking user password');
        }

        /**
         *  Check that new specified password and its retype are the same
         */
        if ($newPassword !== $newPasswordRetype) {
            throw new Exception('New password and password re-type are different');
        }

        /**
         *  Check that new specified password is different that the actual one in database
         */
        if (password_verify($newPassword, $actualPasswordHashed)) {
            throw new Exception('New password must be different then the actual one');
        }

        /**
         *  Hashing new password
         */
        $newPasswordHashed = password_hash($newPassword, PASSWORD_BCRYPT);

        /**
         *  Update in database
         */
        $this->updatePassword($id, $newPasswordHashed);
    }

    /**
     *  Edit user personnal informations
     */
    public function edit(int $id, string $firstName = '', string $lastName = '', string $email = '') : void
    {
        $firstName = \Controllers\Common::validateData($firstName);
        $lastName = \Controllers\Common::validateData($lastName);
        $email = \Controllers\Common::validateData($email);

        if (!empty($email)) {
            // Check that email is a valid email address
            if (\Controllers\Common::validateMail($email) === false) {
                throw new Exception('Email address is invalid');
            }
        }

        /**
         *  Update in database
         */
        $this->model->edit($id, $firstName, $lastName, $email);

        /**
         *  Update sessions variables with new values
         */
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name']  = $lastName;
        $_SESSION['email']      = $email;
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
    private function getHashedPasswordFromDb(int $id) : string
    {
        return $this->model->getHashedPasswordFromDb($id);
    }

    /**
     *  Generate random password
     */
    private function generateRandomPassword() : string
    {
        $combinaison = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@$+-=%|{}[]&";
        $shuffle = str_shuffle($combinaison);

        return substr($shuffle, 0, 16);
    }

    /**
     *  Update user password in database
     */
    private function updatePassword(int $id, string $hashedPassword) : void
    {
        $this->model->updatePassword($id, $hashedPassword);
    }
}
