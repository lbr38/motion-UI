<!DOCTYPE html>
<html>
<?php
if (!defined('ROOT')) {
    define('ROOT', dirname(__FILE__, 2));
}
if (!defined('DATA_DIR')) {
    define('DATA_DIR', '/var/lib/motionui');
}
require_once(ROOT . '/controllers/Autoloader.php');
\Controllers\Autoloader::loadFromLogin();
include_once(ROOT . '/views/includes/head.inc.php');

$loginErrors = array();
$error = 0;

/**
 *  If username and password have been sent
 */
if (!empty($_POST['username']) and !empty($_POST['password'])) {
    /**
     *  Continue if there is no error
     */
    $username = \Controllers\Common::validateData($_POST['username']);
    $mylogin = new \Controllers\Login();

    /**
     *  Checking in database that username/password couple is matching
     */
    try {
        $mylogin->checkUsernamePwd($username, $_POST['password']);

        /**
         *  Getting all user informations in datbase
         */
        $mylogin->getAll($username);

        /**
         *  Starting session
         */
        session_start();

        /**
         *  Saving user informations in session variable
         */
        $_SESSION['username']   = $username;
        $_SESSION['role']       = $mylogin->getRole();
        $_SESSION['first_name'] = $mylogin->getFirstName();
        $_SESSION['last_name']  = $mylogin->getLastName();
        $_SESSION['email']      = $mylogin->getEmail();
        $_SESSION['type']       = 'local';

        /**
         *  If an 'origin' cookie exists then redirect to the specified URI
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
        $loginErrors[] = $e->getMessage();
    }
} ?>
<head>
    <meta charset="utf-8">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="resources/styles/common.css">
    <link rel="stylesheet" type="text/css" href="resources/styles/motionui.css">

    <!-- Favicon -->
    <link rel="icon" href="resources/favicon.ico" />
    <title>Login</title>
</head>

<body>
    <div id="loginDiv-container">
        <div id="loginDiv">
            <h3>AUTHENTICATION</h3>
            <br>
            <form action="/login" method="post" autocomplete="off">
                <input type="text" name="username" placeholder="Username" required />
                <br>
                <input type="password" name="password" placeholder="Password" required />
                <br>
                <button class="btn-large-green" type="submit">Login</button>
            </form>

            <?php
            /**
             *  Display authentication errors if any
             */
            if (!empty($loginErrors)) {
                foreach ($loginErrors as $loginError) {
                    echo '<p>' . $loginError . '</p>';
                }
            } ?>
        </div>
    </div>
</body>
</html>