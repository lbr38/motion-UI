<?php
namespace Controllers\User;

use Exception;

class Login extends User
{
    /**
     *  Login local user
     */
    public function login(string $username, string $password) : void
    {
        try {
            $username = \Controllers\Common::validateData($username);

            /**
             *  Get user Id from username
             */
            $id = $this->getIdByUsername($username);

            /**
             *  If no matching user has been found, throw an exception
             */
            if (empty($id)) {
                throw new Exception('Unknown login');
            }

            /**
             *  Checking in database that username/password couple is matching
             */
            $this->checkUsernamePwd($id, $_POST['password']);

            /**
             *  Getting all user informations in datbase
             */
            $informations = $this->get($id);

            /**
             *  Starting session
             */
            session_start([
                'cookie_secure'   => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
                'cookie_httponly' => true,
            ]);

            /**
             *  Saving user informations in session variables
             */
            $_SESSION['username']   = $username;
            $_SESSION['id']         = $informations['userId'];
            $_SESSION['role']       = $informations['Role_name'];
            $_SESSION['first_name'] = $informations['First_name'];
            $_SESSION['last_name']  = $informations['Last_name'];
            $_SESSION['email']      = $informations['Email'];
            $_SESSION['type']       = 'local';

            /**
             *  Delete 'logout' cookie if it exists
             */
            if (isset($_COOKIE['logout'])) {
                unset($_COOKIE['logout']);
                setcookie('logout', '', time() - 3600, '/');
            }

            /**
             *  If a '.homepage' file exists then redirect to the specified URI
             */
            if (file_exists(DATA_DIR . '/.homepage')) {
                header('Location: /' . trim(file_get_contents(DATA_DIR . '/.homepage')));
                exit();
            }

            /**
             *  If an 'origin' cookie exists then redirect the user to the specified URI
             */
            if (!empty($_COOKIE['origin'])) {
                if ($_COOKIE['origin'] != '/logout') {
                    header('Location: ' . $_COOKIE['origin']);
                    exit();
                }
            }

            /**
             *  Else redirect to default page '/'
             */
            header('Location: /');

            exit();
        } catch (Exception $e) {
            /**
             *  Throw back an exception with generic message to display on login page
             */
            throw new Exception('Invalid login and/or password');
        }
    }
}
