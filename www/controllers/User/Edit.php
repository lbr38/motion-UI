<?php
namespace Controllers\User;

use Exception;
use Controllers\Utils\Validate;
use Controllers\Utils\Random;

class Edit extends User
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new \Models\User\Edit();
    }

    /**
     *  Edit user personal informations
     */
    public function edit(int $id, string $firstName = '', string $lastName = '', string $email = '') : void
    {
        $firstName = Validate::string($firstName);
        $lastName  = Validate::string($lastName);
        $email     = Validate::string($email);

        /**
         *  Check that email is a valid email address
         */
        if (!empty($email)) {
            if (!Validate::email($email)) {
                throw new Exception('Invalid email address format');
            }
        }

        /**
         *  Check that user exists
         */
        if (!$this->existsId($id)) {
            throw new Exception('User does not exist');
        }

        /**
         *  Update informations in database
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
     *  Update user password in database
     */
    private function updatePassword(int $id, string $hashedPassword) : void
    {
        $this->model->updatePassword($id, $hashedPassword);
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
        $password = Random::strongString(32);

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
}
