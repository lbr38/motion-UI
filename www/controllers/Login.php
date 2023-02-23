<?php

namespace Controllers;

use Exception;

class Login
{
    private $model;
    protected $username;
    protected $password;
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $role;

    public function __construct()
    {
        $this->model = new \Models\Login();
    }

    private function setUsername(string $username)
    {
        $this->username = \Controllers\Common::validateData($username);
    }

    private function setPassword(string $password)
    {
        $this->password = \Controllers\Common::validateData($password);
    }

    private function setFirstName(string $firstName = null)
    {
        $this->first_name = \Controllers\Common::validateData($firstName);
    }

    private function setLastName(string $lastName = null)
    {
        $this->last_name = \Controllers\Common::validateData($lastName);
    }

    private function setEmail(string $email = null)
    {
        $this->email = \Controllers\Common::validateData($email);
    }

    private function setRole(string $role)
    {
        $this->role = \Controllers\Common::validateData($role);
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getRole()
    {
        return $this->role;
    }

    /**
     *  Get specified username hashed password from db
     */
    private function getHashedPasswordFromDb(string $username)
    {
        return $this->model->getHashedPasswordFromDb($username);
    }

    /**
     *  Generate random password
     */
    private function generateRandomPassword()
    {
        $combinaison = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@$+-=%|{}[]&";
        $shuffle = str_shuffle($combinaison);

        return substr($shuffle, 0, 16);
    }

    /**
     *  Get username informations
     */
    public function getAll(string $username)
    {
        $userInfo = $this->model->getAll($username);

        $this->setFirstName($userInfo['First_name']);
        $this->setLastName($userInfo['Last_name']);
        $this->setRole($userInfo['Role_name']);
        $this->setEmail($userInfo['Email']);

        return true;
    }

    /**
     *  Get users list from database
     */
    public function getUsers()
    {
        return $this->model->getUsers();
    }

    /**
     *  Add a new user in database
     */
    public function addUser(string $username, string $role)
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
        if ($role != "usage" and $role != "administrator") {
            throw new Exception('Selected user role is invalid');
        }

        /**
         *  Check that username does not already exist
         */
        if ($this->userExists($username) === true) {
            throw new Exception("Username <b>$username</b> is already used");
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
            throw new Exception("Error while hashing user password");
        }

        /**
         *  Converting role as Id
         */
        if ($role == "administrator") {
            $role = 2;
        }
        if ($role == "usage") {
            $role = 3;
        }

        /**
         *  Insert new user in database
         */
        $this->model->addUser($username, $password_hashed, $role);

        /**
         *  Return temporary generated password
         */
        return $password;
    }

    /**
     *  Check that specified username / password couple matches with database
     */
    public function checkUsernamePwd(string $username, string $password)
    {
        $username = \Controllers\Common::validateData($username);

        /**
         *  Check that user exists in database
         */
        if ($this->userExists($username) !== true) {
            throw new Exception('Invalid login and/or password');
        }

        /**
         *  Get user hashed password from database
         */
        $hashedPassword = $this->getHashedPasswordFromDb($username);

        /**
         *  If result is empty then it is anormal, die
         */
        if (empty($hashedPassword)) {
            die();
        }

        /**
         *  If specified password does not matche database passord, then it is invalid
         */
        if (!password_verify($password, $hashedPassword)) {
            throw new Exception('Invalid login and/or password');
        }
    }

    /**
     *  Edit user personnal informations
     */
    public function edit(string $username, string $firstName = null, string $lastName = null, string $email = null)
    {
        $username = \Controllers\Common::validateData($username);

        if (!empty($firstName)) {
            $firstName = \Controllers\Common::validateData($firstName);
        }
        if (!empty($lastName)) {
            $lastName = \Controllers\Common::validateData($lastName);
        }
        if (!empty($email)) {
            $email = \Controllers\Common::validateData($email);

            /**
             *  Check that email is a valid email address
             */
            if (\Controllers\Common::validateMail($email) === false) {
                throw new Exception('Email address is invalid');
            }
        }

        /**
         *  Update in database
         */
        $this->model->edit($username, $firstName, $lastName, $email);

        /**
         *  Update sessions variables with new values
         */
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name']  = $lastName;
        $_SESSION['email']      = $email;
    }

    /**
     *  Changing user password
     */
    public function changePassword(string $username, string $actualPassword, string $newPassword, string $newPasswordRetype)
    {
        $username = \Controllers\Common::validateData($username);

        /**
         *  Check that user exists
         */
        if ($this->userExists($username) !== true) {
            throw new Exception("User <b>$username</b> does not exist");
        }

        /**
         *  Check that the actual password is valid
         */
        $this->checkUsernamePwd($username, $actualPassword);

        /**
         *  Now checking that actual password matches actual password in database
         */

        /**
         *  Get actual hashed password in database
         */
        $actualPasswordHashed = $this->getHashedPasswordFromDb($username);

        /**
         *  If result is empty then it is anormal, die
         */
        if (empty($actualPasswordHashed)) {
            die();
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
        $this->model->updatePassword($username, $newPasswordHashed);
    }

    /**
     *  Reset specified user password
     */
    public function resetPassword(string $username)
    {
        $username = \Controllers\Common::validateData($username);

        /**
         *  Check that user exists
         */
        if ($this->userExists($username) !== true) {
            throw new Exception("User <b>$username</b> does not exist");
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
            \Controllers\Common::printAlert("Error while creating a new password for user <b>$username</b>", 'error');
        }

        /**
         *  Adding new hashed password in database
         */
        $this->model->updatePassword($username, $hashedPassword);

        /**
         *  Return new password
         */
        return $password;
    }

    /**
     *  Delete specified user
     */
    public function deleteUser(string $username)
    {
        $username = \Controllers\Common::validateData($username);

        /**
         *  Check that user exists
         */
        if ($this->userExists($username) !== true) {
            throw new Exception("User <b>$username</b> does not exist");
        }

        /**
         *  Disabling user in database
         *  The user is being kept in database for history reasons but its status is set on 'deleted' and the user become unusuable
         *  Its password is removed from database
         */
        $this->model->deleteUser($username);
    }

    /**
     *  Check if user exists in database
     */
    private function userExists(string $username)
    {
        $user = $this->model->userExists($username);

        if (empty($user)) {
            return false;
        }

        return true;
    }
}
